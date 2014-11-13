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

abstract class filter {
    
    const OPERATOR_CONTAINS             = 1;
    const OPERATOR_DOESNOTCONTAIN       = 2;
    const OPERATOR_ISEQUALTO            = 3;
    const OPERATOR_STARTSWITH           = 4;
    const OPERATOR_ENDSWITH             = 5;
    const OPERATOR_ISEMPTY              = 6;
    const OPERATOR_GREATERTHAN          = 7;
    const OPERATOR_GREATERTHANOREQUAL   = 8;
    const OPERATOR_LESSTHAN             = 9;
    const OPERATOR_LESSTHANOREQUAL      = 10;
    
    
    public function __construct($fieldname, $fieldprefix = '', $elementname = null, $elementlabel = null) {
        $this->fieldname    = $fieldname;
        $this->fieldprefix  = $fieldprefix;
        $this->elementname  = $elementname;
        $this->elementlabel = $elementlabel;
        $this->name         = empty($elementname) ? $fieldname : $elementname;
    }
    public function get_data($data){}
    public function get_sql(){}
   
}