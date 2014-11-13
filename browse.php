<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');
require_once('locallib.php');

$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 50, PARAM_INT);
$sort         = optional_param('sort', 'id', PARAM_TEXT);
$direction    = optional_param('direction', 'ASC', PARAM_TEXT);

$baseurl = new moodle_url('/local/coursearchiver/browse.php', array('page' => $page,
                                                                    'perpage' => $perpage,
                                                                    'sort' => $sort,
                                                                    'direction' => $direction));

admin_externalpage_setup('coursearchiver-browse');

$pluginconfig = course_archiver::get_config();

$filterfields  = coursearchiver_get_enabled_filters();

$newfilterform = new \local_coursearchiver\forms\new_filter($baseurl, array('filterfields' => $filterfields));
if ($newfilterform->is_submitted()) {
    $newfilterform->save_filter_data();
    redirect($baseurl);
}

$activefiltersform = new \local_coursearchiver\forms\active_filters($baseurl, array('filterfields' => $filterfields));
if ($activefiltersform->is_submitted()) {
    $activefiltersform->remove_filter_data();
    redirect($baseurl);
}

$selectedform = new \local_coursearchiver\forms\selected();
if ($selectedform->is_submitted()) {
    $data = $selectedform->get_data();
    if (isset($data->valuescontainer) and (isset($data->queue) or isset($data->dequeue))) {
        $cids = explode(',', $data->valuescontainer);
        $courses = $DB->get_records_list('course', 'id', $cids, '', 'id, fullname');
        if ($courses) {
            $info = array();
            foreach($courses as $course) {
                try {
                    if (isset($data->queue)) {
                        \course_archiver::add_to_queue($course);
                        $info[] = $course->fullname . " queued";
                    }
                    if (isset($data->dequeue)) {
                        \course_archiver::remove_from_queue($course);
                        $info[] = $course->fullname . " dequeued";
                    }
                } catch (moodle_exception $e) {
                    $info[] = $course->fullname . " already in queue";
                }    
            }
            redirect($baseurl, implode(', ', $info), 2);
        }
    }
    redirect($baseurl);
}
// Construct SQL fragments.
$params = array('siteid' => SITEID);
$wheresql = '';
foreach($filterfields as $filter) {
    list($fsql, $fparams) = $filter->get_sql();
    if (!empty($fsql)) {
        $wheresql .= " AND ". $fsql;
    }
    if (!empty($fparams)){
        $params = array_merge($fparams, $params);
    }
}
$countsql   = "SELECT COUNT(1) ";
$fieldsql   = "SELECT c.id, c.shortname, c.fullname, c.idnumber, c.summary, c.visible, c.timecreated, ca.timequeued, ca.status ";
$basesql    = "FROM {course} c 
          LEFT JOIN {course_archiver} ca 
                 ON ca.courseid = c.id 
              WHERE c.id != :siteid";
$orderby    = "ORDER BY c.id";

echo $OUTPUT->header();

$newfilterform->display();
$activefiltersform->display();

$rowsmatched = $DB->count_records_sql($countsql . $basesql . $wheresql, $params);
if ($rowsmatched) {
    $selectallcheckbox = html_writer::div(html_writer::checkbox('sa-checkbox', null, false, '', null), 'selectall');

    $tableheaders = array($selectallcheckbox,
                          get_string('course'),
                          get_string('timecreated', 'local_coursearchiver'),
                          get_string('idnumber'),
                          get_string('queued', 'local_coursearchiver'),
                          );

    $table = new html_table();
    $table->id = 'coursearchiver-list-' . uniqid();
    $table->attributes['class'] = 'admintable generaltable';
    $table->head = $tableheaders;

    // TODO - Need to set a max limit.
    $limit = $perpage;
    $offset = $page * $limit;
    $records = $DB->get_records_sql($fieldsql . $basesql . $wheresql . $orderby, $params, $offset, $limit);
    foreach ($records as $record) {
        $row = array();
        $row[] = html_writer::checkbox('sa-item', $record->id, null, false);
        
        $hidden = '';
        if (!$record->visible) {
            $hidden .= $OUTPUT->pix_icon('i/show', get_string('coursehidden'), 'moodle', array('style' => 'margin-right:3px;'));
        }
        
        $row[] = $hidden . html_writer::link(new moodle_url('/course/view.php', array('id' => $record->id)), 
                                   $record->fullname);
        
        $row[] = userdate($record->timecreated);
        $row[] = $record->idnumber;
        $row[] = isset($record->timequeued) ? get_string('yes') : get_string('no');
        $table->data[] = $row;
    }
    echo $OUTPUT->heading(get_string('recordsfound','local_coursearchiver', $rowsmatched));
    $pagination = new paging_bar($rowsmatched, $page, $limit, $PAGE->url);
    echo $OUTPUT->render($pagination);
    echo html_writer::table($table);
    $selectedform->display();
} else {
    echo $OUTPUT->heading(get_string('nothingtodisplay'));
}
echo $OUTPUT->footer();
