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

class text_filter extends filter {
    public function __construct($fieldname, $fieldprefix = '', $elementname = null, $elementlabel = null) {
        parent::__construct($fieldname, $fieldprefix, $elementname, $elementlabel);
        $this->filtertype = 'text';
    }
    /**
     * 
     */
    public function add_to_form($form) {
        $elements = array();
        $elements [] = $form->createElement('select', $this->name.'_operator', null, self::available_operators());
        $elements [] = $form->createElement('text', $this->name, null);
        $form->addElement('group', $this->name.'_group', $this->elementlabel, $elements, '', false);
        $form->setType($this->name, PARAM_RAW);
        $form->disabledIf($this->name, $this->name.'_operator', 'eq', filter::OPERATOR_ISEMPTY);
    }
    /**
     * Returns an array of available operators
     * @return array of available operators
     */
    public static function available_operators() {
        return array(filter::OPERATOR_CONTAINS         => get_string('contains', 'filters'),
                     filter::OPERATOR_DOESNOTCONTAIN   => get_string('doesnotcontain', 'filters'),
                     filter::OPERATOR_ISEQUALTO        => get_string('isequalto', 'filters'),
                     filter::OPERATOR_STARTSWITH       => get_string('startswith', 'filters'),
                     filter::OPERATOR_ENDSWITH         => get_string('endswith', 'filters'),
                     filter::OPERATOR_ISEMPTY          => get_string('isempty', 'filters'));
    }
    
    public function get_data($form) {
        $data       = (object) $form->exportValues();
        $name       = $this->name;
        $operator   = $this->name.'_operator';

        if (array_key_exists($operator, $data)) {
           if ($data->$operator != filter::OPERATOR_ISEMPTY and $data->$name == '') {
                // No data - no change except for empty filter.
                return false;
            }
            // If field value is set then use it, else it's null.
            $value = null;
            if (isset($data->$name)) {
                $value = $data->$name;
            }
            return array('operator' => $data->$operator,
                         'value' => $value);
        }
        return false;
    }
    
    public function get_match_description($data) {
        $operators = self::available_operators();
        return get_string($this->name, 'local_coursearchiver') . ' ' . $operators[$data['operator']] . ' ' . $data['value'];
    }
    
    public function get_sql() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/local/coursearchiver/locallib.php');
        
        static $counter = 0;
        
        $sql    = '';
        $params = array();
        $placeholder = 'ex_text'.$counter++;
        $fieldname = $this->fieldname;
        if (!empty($this->fieldprefix)) {
            $fieldname = $this->fieldprefix . '.' . $this->fieldname;
        }
        
        $data = coursearchiver_cache_get($this->fieldname);
        
        if ($data) {
            foreach ($data as $item) {
                $operator = isset($item['operator']) ? $item['operator'] : false;
                $value = isset($item['value']) ? $item['value'] : false;
                if ($operator and $value) {
                    switch($operator) {
                        case filter::OPERATOR_CONTAINS: // Contains.
                            $itemsql = $DB->sql_like($fieldname, ":$placeholder", false, false);
                            $itemparams[$placeholder] = "%$value%";
                            break;
                        case filter::OPERATOR_DOESNOTCONTAIN: // Does not contain.
                            $itemsql = $DB->sql_like($fieldname, ":$placeholder", false, false, true);
                            $itemparams[$placeholder] = "%$value%";
                            break;
                        case filter::OPERATOR_ISEQUALTO: // Equal to.
                            $itemsql = $DB->sql_like($fieldname, ":$placeholder", false, false);
                            $itemparams[$placeholder] = "$value";
                            break;
                        case filter::OPERATOR_STARTSWITH: // Starts with.
                            $itemsql = $DB->sql_like($fieldname, ":$placeholder", false, false);
                            $itemparams[$placeholder] = "$value%";
                            break;
                        case filter::OPERATOR_ENDSWITH: // Ends with.
                            $itemsql = $DB->sql_like($fieldname, ":$placeholder", false, false);
                            $itemparams[$placeholder] = "%$value";
                            break;
                        case filter::OPERATOR_ISEMPTY: // Empty.
                            $itemsql = "$fieldname = :$placeholder";
                            $itemparams[$placeholder] = '';
                            break;
                        default:
                            
                    }
                    $sql .= ' ' . $itemsql;
                    $params = array_merge($params, $itemparams);
                }
            }
        }
        return array($sql, $params);
    }
}