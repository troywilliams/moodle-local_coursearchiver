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

namespace local_coursearchiver\filters;

class checkbox_filter extends filter {
    public function __construct($fieldname, $fieldprefix = '', $elementname = null, $elementlabel = null) {
        parent::__construct($fieldname, $fieldprefix, $elementname, $elementlabel);
        $this->filtertype = 'checkbox';
    }
    public function add_to_form($form) {
        $elements = array();
        $elements[] = $form->createElement('checkbox', $this->name, null, '');
        $form->addElement('group', $this->name.'_group', $this->elementlabel, $elements, '', false);
    }
    
    public function get_data($form) {
        $data       = (object) $form->exportValues();
        $name       = $this->name;

        if (array_key_exists($name, $data) and $data->$name !== '') {
            return array('value' => $data->$name);
        }
        return false;
    }
    
    public function get_match_description($data) {
        return get_string($this->name, 'local_coursearchiver');
    }
    
    public function get_sql() {
        static $counter = 0;
        
        $sql    = '';
        $params = array();
        $placeholder = 'ex_cb'.$counter++;
        $fieldname = $this->fieldname;
        if (!empty($this->fieldprefix)) {
            $fieldname = $this->fieldprefix . '.' . $this->fieldname;
        }
        
        $data = coursearchiver_cache_get($this->fieldname);
        if ($data) {
            $sql = "$this->fieldname >= 1"; // Anything greater than 1.
        }
        return array($sql, $params);
    }
}