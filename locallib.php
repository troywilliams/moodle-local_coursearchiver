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

defined('MOODLE_INTERNAL') || die();

function coursearchiver_get_configured_filters() {
    return array(

        'shortname' => array(
            'type' => 'text',
            'fieldprefix' => 'c',
            'elementname' => '',
            'elementlabel' => get_string('shortname', 'local_coursearchiver')
        ),

        'fullname' => array(
            'type' => 'text',
            'fieldprefix' => 'c',
            'elementname' => '',
            'elementlabel' => get_string('fullname', 'local_coursearchiver')
        ),

        'timecreated' => array(
            'type' => 'date',
            'fieldprefix' => 'c',
            'elementname' => '',
            'elementlabel' => get_string('timecreated', 'local_coursearchiver')
        ),

        'timequeued' => array(
            'type' => 'checkbox',
            'fieldprefix' => 'ca',
            'elementname' => 'queued',
            'elementlabel' => get_string('queued', 'local_coursearchiver')
        )

    );
}

function coursearchiver_get_enabled_filters() {
    $filterfields = array();
    $filterfieldsconfig = coursearchiver_get_configured_filters();
    foreach($filterfieldsconfig as $fieldname => $config) {
        $type           = $config['type'];
        $classname      = '\\local_coursearchiver\\filters\\' . $type . '_filter';
        $fieldprefix    = $config['fieldprefix'];
        $elementname    = $config['elementname'];
        $elementlabel   = $config['elementlabel'];
        $filter = new $classname($fieldname, $fieldprefix, $elementname, $elementlabel);
        $filterfields[$fieldname] = $filter;
    }
    return $filterfields;
}


function coursearchiver_cache_empty() {
        global $SESSION;

        if (isset($SESSION->coursearchiver_filters) and is_array($SESSION->coursearchiver_filters)) {
            foreach($SESSION->coursearchiver_filters as $filter) {
                if (is_array($filter)) {
                    return false;
                }
            }
        }
        return true;
    }

function coursearchiver_cache_get($field) {
    global $SESSION;
    if (!isset($SESSION->coursearchiver_filters[$field])) {
        $SESSION->coursearchiver_filters[$field] = false;
        return false;
    }
    return $SESSION->coursearchiver_filters[$field];
}

function coursearchiver_cache_purge() {
    global $SESSION;
    return $SESSION->coursearchiver_filters = null;
}

function coursearchiver_cache_remove($field, $key) {
    global $SESSION;
    if (isset($SESSION->coursearchiver_filters[$field][$key])) {
        unset($SESSION->coursearchiver_filters[$field][$key]);
    }
}

function coursearchiver_cache_set($field, $data) {
    global $SESSION;
    if (!isset($SESSION->coursearchiver_filters[$field])) {
        $SESSION->coursearchiver_filters[$field] = array();
    }
    return $SESSION->coursearchiver_filters[$field][] = $data;
}

