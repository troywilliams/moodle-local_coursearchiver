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

class new_filter extends \moodleform {
    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        
        $this->filterfields = $this->_customdata['filterfields'];
        
        $mform->addElement('header', 'newfilter', get_string('newfilter', 'filters'));
        
        // Add filter
        foreach ($this->filterfields as $filterfield) {
            $filterfield->add_to_form($mform);
        }
        
        // Add button.
        $mform->addElement('submit', 'addfilter', get_string('addfilter', 'filters'));
    }
    
    public function save_filter_data() {
        $mform = $this->_form;
 
        foreach ($this->filterfields as $filterfield) {
            $data = $filterfield->get_data($mform);
            if ($data) {
                coursearchiver_cache_set($filterfield->fieldname, $data); // checkbox needs to be reset
            }
        }

    }

}