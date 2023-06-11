<?php 
 error_reporting(E_ALL);
 ini_set("display_errors", 1);

 
include 'lib/Parent_child_relation.php';
include 'data/accounting_data.php';
echo "<pre>";
$parent_relation = new Parent_child_relation(); 

      

        // get tree 
        //  $data = $parent_relation->get($accounting_data, ["type" => "tree"]);
       $data = $parent_relation->get_tree($accounting_data);
      
       //get list 
        //  $data = $parent_relation->get($accounting_data, ["type" => "list"]);
        //   $data = $parent_relation->get_list($accounting_data);
        
        //get optgroup 
        //  $data = $parent_relation->get($accounting_data, ["type" => "optgroup"]);
        //   $data = $parent_relation->get_optgroup($accounting_data);
       
        //get optgroup 
        //  $data = $parent_relation->get($accounting_data, ["type" => "dropdown"]);
      //  $data = $parent_relation->get_dropdown($accounting_data);
        print_r($data);

?>
