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

class active_filters extends \moodleform {
    /**
     * Form definition.
     */
    public function definition() {
        $mform              = $this->_form;
        $this->filterfields = $this->_customdata['filterfields'];
        
        
        if (!coursearchiver_cache_empty()) {
             $mform->addElement('header', 'actfilterhdr', get_string('actfilterhdr', 'filters'));
            foreach ($this->filterfields as $filterfield) {
                $cachedata = coursearchiver_cache_get($filterfield->fieldname);
                if ($cachedata) {
                    foreach ($cachedata as $key => $data) {
                        $matchdescription = $filterfield->get_match_description($data);
                        $mform->addElement('checkbox', 'filter['.$filterfield->fieldname.']['.$key.']', null, $matchdescription);
                    }
                    
                }

            }
             // Remove button group
            $elements = array();
            $elements[] = &$mform->createElement('submit', 'removeselected', get_string('removeselected', 'filters'));
            $elements[] = &$mform->createElement('submit', 'removeall', get_string('removeall', 'filters'));
            $mform->addElement('group', 'actfiltergrp', '', $elements, ' ', false);
        
        }

    }
    
    public function remove_filter_data() {
        $formdata = $this->get_data();
        if (isset($formdata->removeall)) {
            coursearchiver_cache_purge();
            return;
        } else if (isset($formdata->removeselected)) {
            if (isset($formdata->filter)) {
                foreach ($formdata->filter as $field => $filterdata) {
                    foreach($filterdata as $key => $value) {
                        coursearchiver_cache_remove($field, $key);
                    }
                }
            }
        }
        return;
    }
}