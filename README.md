**Userguide on Parent Child Relationship class**


**Class name and Init:**
Class name is Parent_relation and you can init this following way 


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



**List object with parent ID:**
Array or object as like following with index parent_id 

```
[0] => stdClass Object
        (
            [id] => 9
            [parent_id] => 0
            [title] =>   Assets
            [description] => An asset is generally any useful thing or something that holds value.
        )
[1] => stdClass Object
        (
            [id] => 9
            [parent_id] => 1
            [title] => Current Assets
            [description] => Current assets include cash and cash equivalents, inventory, and accounts receivable other assets that can convert into cash in one year.
        )
[1] => stdClass Object
        (
            [id] => 9
            [parent_id] => 1
            [title] => Fixed Assets  
            [description] =>  Fixed assets refer to long-term tangible assets that are used in the operations of a business. 
        )
```



**Call Method:**
You can call following one method for all with type in option params 

```get($objects, $options = [])```

By $options,  you can set following params: 


fields  - optional  return filed, by default return all fields 

type - this is also optional. If not set this, by default, return list object 
 
 type will be 
 list - provide list objects with parent child order
 tree - provide Tree objects with parent child order
 optgroup - provide  objects with parent child order which is fitted for optgroup
dropdown - provide  objects with parent child order which is fitted for dropdown/normal select   and Parent child indicates using |_ 
$options will contain other options such as $key $value, $is_long_name(if true, it will provide a name with all parent) etc

Suppose your list is $accounting_charter and you want tree type object, then  you can call following way

```$parent_relation->get($accounting_charter,  [‘type’=>’tree’])```

Or you can also call following way 

```$parent_relation->get_tree($accounting_charter)```

 


