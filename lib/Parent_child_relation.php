<?php


/**
 * Project Name     : Parent child relation 
 *
 * Developer        : Md. Saidul Haque
 * Web and Profile  : http://saidulhaque.com
 * Help             : 
 *
 * @Class           : Mp_tree
 * @Objective       : to Build Library or Single Class for maintain Parent child relation and provide Tree, Ordering array/object, dropdown and optgroup etc 
 */
class Parent_child_relation {

    var $tree = [];
    var $parent_title = "";
    var $parent_id = 0;
    var $title = "name";
    var $is_key_value = false;
    //options
    var $type = ""; //return type | list,tree, dropdwon and optgroup
    var $fields = []; // specify filelds for return data 
    var $key = ''; // key is for dropdown/optgroup key  
    var $value = ''; // name is for dropdown/optgroup level value
    var $is_long_name = false; //if true, it will provide a name with previous parent
    var $is_array = false; // return data array or object. if true, it will return array, otherwise return object 
    var $indicattor = "_";
    var $concat_fields = [];
    var $concat_by = "";
    var $order_by = "";
    var $order = " ";

    function __construct() {
        // parent::Controller();
    }

    /*     * *********************************************************************************************
     * @method get | is commont method for this class 
     * @access    public
     * create list with $objects,$fields, $list_type, $options- ()
     * $list_type will be 
     *  1. list - provide list objects with parent child order
     *  2. tree - provide Tree objects with parent child order
     *  3. optgroup - provide  objects with parent child order which is fitted for optgroup
     *  4. dropdown - provide  objects with parent child order which is fitted for dropdown/normal selectt and Parent chiled indicates using |_ 
     * $options will contain other options such as $key $value, $is_long_name(if true, it will provide a name with all parent) etc
     * return objects
     * ****************************************************************************************** */

    function get($objects, $options = []) {

        $this->type = isset($options['type']) ? $options['type'] : '';
        $new_objects = [];
        if ($this->type == 'dropdown') {
            $new_objects = $this->get_dropdown($objects, $options);
        } elseif ($this->type == 'optgroup') {
            $new_objects = $this->get_optgroup($objects, $options);
        } elseif ($this->type == 'optgroup_ci') {
            $new_objects = $this->get_optgroup_ci($objects, $options);
        } elseif ($this->type == 'tree') {
            $new_objects = $this->get_tree($objects, $options);
        } elseif ($this->type == 'autosuggest') {
            $new_objects = $this->get_autosuggest($objects, $options);
        } else {
            $new_objects = $this->get_list($objects, $options);
        }
        return $new_objects;
    }

    function get_list($objects, $options = []) {
        return $this->_create_list($objects, $options);
    }

    function get_tree($objects, $options = []) {
        return $this->_create_tree($objects, $options);
    }

    function get_dropdown($objects, $options = []) {
        $elements = $this->get_list($objects, $options);
        $data_list = $this->_create_dropdown($elements);
        return $data_list;
    }

    function get_optgroup($objects, $options = []) {
        $elements = $this->get_list($objects, $options);
        $data_list = $this->_create_optgroup($elements);
        return $data_list;
    }

    function get_optgroup_ci($objects, $options = []) {
        $elements = $this->get_list($objects, $options);
        $data_list = $this->_create_optgroup_ci($elements);
        return $data_list;
    }

    /*     * *********************************************************************************************
     * @method get_dropdown
     * @access    public function 
     * generate dropdown list  
     * return array or object according request. By fault, return object, is_array is one than return array
     * ****************************************************************************************** */

    function get_autosuggest($objects, $options = []) {
        $elements = $this->get_list($objects, $options);

        $final_items = [];
        foreach ($elements as $element) {
            $element = (object) $element;
            if (!empty($element->children)) {
                foreach ($element->children as $item) {
                    $temp = [];
                    $temp["id"] = $item->id;
                    $temp["title"] = $item->title;
                    $final_items[] = $temp;
                }
            } else {
                $temp = [];
                $temp["id"] = $element->id;
                $temp["title"] = $element->title;
                $final_items[] = $temp;
            }
        }

        return $final_items;
    }

    // create 
    function _create_tree($objects, $options = []) {
        $this->_set_options($objects, $options);
        return $this->tree;
    }

    function _create_list($objects, $options = []) {
        $this->_set_options($objects, $options);
        return $this->_revert($this->tree);
    }

    function _create_dropdown($objects) {
        $dropdown_list = [];
        foreach ($objects as $object) {
            $object = (object) $object;
            if (!empty($this->concat_fields)) {
                $value = $this->concat_by;
            } else {
                $value = isset($object->{$this->title}) ? $object->{$this->title} : "Field name not match";
            }
            $key = $object->id;

            if ($this->is_long_name) {
                $value = $object->long_name;
                if ($this->is_key_value) {
                    $key = $object->{$this->key};
                }
            } else {
                $value = str_repeat($this->indicattor, $object->level) . $value;
                if ($this->is_key_value) {
                    $value = str_repeat($this->indicattor, $object->level) . $value;
                    $key = $object->{$this->key};
                }
            }
            $dropdown_list[$key] = $value;
        }

        return $dropdown_list;
    }

    function _create_optgroup($objects) {

        $data_list = [];
//       echo $this->title;exit; 

        $child = [];
        $parent_title = "";
        $parent_id = 0;
        foreach ($objects as $object) {
            $object = (object) $object;
            $key = $object->id;
            if ($this->is_key_value) {
                $key = $object->{$this->key};
            }

            $value = isset($object->{$this->title}) ? $object->{$this->title} : "Field name not match";
            if ($this->is_long_name) {
                $value = $object->long_name;
            }
            if ($object->level == 0) {
                if (!empty($child)) {
                    $data_list[] = ['title' => $parent_title, "parent_id" => $parent_id, 'children' => $child];
                }
                $child = [];
                $parent_title = $value;
                $parent_id = $object->id;
            } else {
                $child[$key] = $object;
            }
        }
        if (!empty($child)) {
            $data_list[] = ['title' => $parent_title, 'children' => $child];
        }

        return $data_list;
    }

    // CodeIgnitor based optgroupt 
    function _create_optgroup_ci($objects) {

        $data_list = [];
//       echo $this->title;exit; 
        pr($objects);
        exit;
        $child = [];
        $parent_title = "";
        $parent_id = 0;
        foreach ($objects as $object) {
            $object = (object) $object;
            $key = $object->id;
            if ($this->is_key_value) {
                $key = $object->{$this->key};
            }

            $value = isset($object->{$this->title}) ? $object->{$this->title} : "Field name not match";
            if ($this->is_long_name) {
                $value = $object->long_name;
            }
            //echo $value."<br/>"; 
            if ($object->level == 0) {
                if (!empty($child)) {
                    $data_list[$parent_title] = $child;
                }
                $child = [];
                $parent_title = $value;
                $parent_id = $object->id;
            } else {
                $child[$key] = $value;
            }
        }
        if (!empty($child)) {
            $data_list[$parent_title] = $child;
        }
//pr($data_list); exit; 
        return $data_list;
    }

    /*     * *********************************************************************************************
     * @method _revert
     * @access    private function 
     * revert tree to array
     * return array or object according request. By fault, return object, is_array is one than return array
     * ****************************************************************************************** */

    function _revert($array) {
        $result = array();
        foreach ($array as $key => $row) {
            $row = (array) $row;
//            pr($row);exit; 
            $present_row = $row;
            if (isset($row['children']) && count($row['children']) > 0) {
                unset($present_row['children']);
                if (!$this->is_array) {
                    $present_row = (object) $present_row;
                }
                $result[] = $present_row;
                $result = array_merge($result, $this->_revert($row['children']));
            } else {
                if (!$this->is_array) {
                    $present_row = (object) $present_row;
                }
                $result[] = $present_row;
            }
        }

        return $result;
    }

    /*     * *********************************************************************************************
     * @method list_array
     * @access    private function
     * create list with table fileds and adjust with params field and set Title 
     * return array
     * ****************************************************************************************** */

    function convert_to_object($Objects) {
        $obj = [];

        foreach ($Objects as $Object) {
            $obj[] = is_array($Object) ? (object) $Object : $Object;
        }

        return $obj;
    }

    function _list_array($Objects) {
        $Objects = $this->convert_to_object($Objects);
        $listArray = [];
        $sort_order = [];
      
        $pid = 1;
        foreach ($Objects as $Object) {
            if (isset($Object->parent_id) && $Object->parent_id == 0) {
                $pid = 0;
            }
//            pr($Object); exit;
//            pr($this->fields); exit;
            $newObject = [];
            $id = $Object->id;
            if (!empty($this->fields)) {
                foreach ($this->fields as $field) {
                    $newObject[$field] = $Object->{$field};
                    if ($field == "title" && !$this->value) {
                        $this->title = "title";
                    }
                }
            } else {
                // $newObject = array('id' => $id, 'parent_id' => $Object->parent_id, 'name' => $Object->name, 'alias' => $Object->alias, 'description' => $Object->description);

                $newObject = array('id' => $id, 'parent_id' => isset($Object->parent_id) ? $Object->parent_id : 0);
                if (isset($Object->name)) {
                    $newObject['name'] = $Object->name;
                } else if (isset($Object->title)) {
                    $newObject['title'] = $Object->title;
                    $this->title = "title";
                }

                if (isset($Object->description)) {
                    $newObject['description'] = $Object->description;
                }

                if (isset($Object->alias)) {
                    $newObject['alias'] = $Object->alias;
                }
            }

            
//            echo $this->order_by; exit; 
            $listArray[] = $newObject;
            $order = isset($newObject['sort_order']) ? $newObject['sort_order'] : '';
            $order = isset($newObject[$this->order_by]) ? $newObject[$this->order_by] : '';

            if (isset($newObject['sort_order'])) {
                $sort_order[] = $order;
            }
        }
//   pr($listArray);     
//pr($sort_order);exit;
        if (!empty($sort_order)) {
            array_multisort($listArray, SORT_ASC, $sort_order, SORT_ASC);
            array_multisort($listArray, $sort_order);
        }
//pr($listArray);exit;
        if ($pid == 1 && !empty($listArray)) {
            $this->parent_id = $listArray[0]['parent_id'];
        }

        return $listArray;
    }

    /*     * *********************************************************************************************
     * @method _build_tree
     * @access    private function
     * create tree with list array according to parent ID 
     * return array
     * ****************************************************************************************** */

    function _build_tree(array $elements, $parentId = 0, $level = 0, $parent = "") {
        $branch = array();
        foreach ($elements as $element) {
            $element["level"] = $level;
            $element["parent"] = $parent;
            $this->title = isset($element[$this->title]) && $element[$this->title] ? $this->title : "title";
            if ($this->is_long_name) {
                $element["long_name"] = $parent . "> " . $element[$this->title];
            }
            if ($element['parent_id'] == $parentId) {
//                echo $this->title; exit;
                $children = $this->_build_tree($elements, $element['id'], $level + 1, $parent . "> " . $element[$this->title]);
                if ($children) {
                    $element['children'] = $children;
                }
                //exception for tree 
                if (!$this->is_array) {
                    $element = (object) $element;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    /*     * *********************************************************************************************
     * @method _set_options
     * @access    private function
     * set all memeber vvariable 
     * return N/A
     * ****************************************************************************************** */

    function _set_options($objects, $options = []) {
//        pr($objects);
//        exit;
        $this->fields = [];
        if (!empty($options)) {
            $this->key = isset($options['key']) ? $options['key'] : $this->key;
            if (isset($options['value']) && is_array(isset($options['value']))) {
                $concat = isset($options['value']);
                $this->concat_fields = isset($concat['concat_fields']) ? $concat['concat_fields'] : $this->concat_fields;
                $this->concat_by = isset($concat['concat_by']) ? $concat['concat_by'] : $this->concat_by;
            } else {
                $this->value = isset($options['value']) ? $options['value'] : $this->value;
            }

            $this->order_by = isset($options['order_by']) ? $options['order_by'] : "parent_id";
            $this->order = isset($options['order']) ? $options['order'] : "asc";

            $this->is_array = isset($options['is_array']) ? $options['is_array'] : $this->is_array;
            $this->indicattor = isset($options['indicattor']) ? $options['indicattor'] : $this->indicattor;

            $this->is_long_name = isset($options['is_long_name']) ? $options['is_long_name'] : $this->is_long_name;
            $fields = isset($options['fields']) ? $options['fields'] : [];
            if (!empty($fields) && !in_array("parent_id", $fields)) {
                $fields[] = "parent_id";
            }
            $this->fields = $fields;
            $title = isset($fields['title']) ? "title" : "name";
            $this->value = $this->value ? $this->value : $title;
            $this->title = $this->value;

            $this->is_key_value = $this->key && $this->value ? true : $this->is_key_value;
        }

        $listArray = $this->_list_array($objects);
//        pr($listArray);
//        exit;
        $treeObjects = $this->_build_tree($listArray, $this->parent_id);
        $this->tree = $treeObjects;
    }

    function generate_inheritance($inheritance, $name = '') {
        $inheritance_json_arr = json_decode($inheritance);
        $text = [];
        if (!empty($inheritance_json_arr)) {
            // sort($inheritance_json_arr);
            foreach ($inheritance_json_arr as $inheritance_json) {
                if (isset($inheritance_json->name) && $inheritance_json->name) {
                    $text[] = $inheritance_json->name;
                }
            }
        }

        $text[] = $name;
        return !empty($text) ? implode(" > ", $text) : "";
    }
}
