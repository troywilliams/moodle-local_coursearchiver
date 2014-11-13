<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_coursearchiver\forms;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');

require_once('locallib.php');

class selected extends \moodleform {
    /**
     * Define this form - is called from parent constructor.
     */
    public function definition() {
        global $PAGE;
        
        // Javascript selectall checkbox.
        $PAGE->requires->js('/local/coursearchiver/javascript/selectall.js');
        $PAGE->requires->js_init_call('M.local_coursearchiver.selectall.init', array('sa-checkbox', 'valuescontainer'));
        $PAGE->requires->string_for_js('noneselected', 'local_coursearchiver');
        
        $mform = $this->_form;
        // Visible elements.
        $mform->addElement('hidden', 'valuescontainer');
        $mform->setType('valuescontainer', PARAM_SEQUENCE);

        // Remove button group
        $elements = array();
        $elements[] = &$mform->createElement('submit', 'queue', get_string('queueselected', 'local_coursearchiver'), array('class'=>'sa-submit'));
        $elements[] = &$mform->createElement('submit', 'dequeue', get_string('dequeueselected', 'local_coursearchiver'), array('class'=>'sa-submit'));

        $mform->addElement('group', 'selectallsubmitgroup', '', $elements, ' ', false);
    }
    
}