<?php

// function insert
function insert($the_list ,$name ,$number ,$city ,$type1 ,$type2 ,$email)
{

    if ($the_list == null) {
        $i =  0 ;
    } else {
        $i = count($the_list) ;
        while (in_array($name,array_column($the_list, 'name'))){
            echo "The name you entered already exist.\nPlease rewrite a new name :)\n";
            $name = readline() ;
        }  
    }
    
    $pattern = "/^((09(1[0-9]|2[1-9]|3[1-9])\d{7})|(\+989(1[0-9]|2[1-9]|3[1-9])\d{7})|(0[0-9]{2,}[0-9]{7,}))$/";
    while(preg_match($pattern,$number)==0){
        echo "Sorry but your number format is wrong. please try again.\n";
        $number = readline();
    }

    $the_list[$i]['name'] =  $name ;
    $the_list[$i]['number'] = $number ;
    $the_list[$i]['city'] = $city ;
    $the_list[$i]['type1'] = $type1 ;
    $the_list[$i]['type2'] = $type2 ;
    $the_list[$i]['email'] = $email ;
    echo "Done! :)" ;
    return $the_list;
}

// update  function
function update($the_list ,$name ,$field_name , $new_info){
    if (in_array($name,array_column($the_list, 'name'))) {
        $i = array_search($name,array_column($the_list,'name'));
        $the_list[$i][$field_name] = $new_info ;
        echo "Done! :)" ;
    }
    else{
        echo "This name doesn't exist! :(" ;
    }
    return $the_list ;
}


// get function
function get($the_list ,$field_name ,$item)
{

    $filteredArray = 
    array_filter($the_list, function($element) use($field_name , $item){
    return isset($element[$field_name]) && $element[$field_name] == $item;
    });

    if ($filteredArray == null){
        echo "This item doesn't exist! :(" ;
        return null ;
    } else {
        return $filteredArray ;
    }
}

// clear function
function clear($the_list ,$name)
{
    if (in_array($name,array_column($the_list, 'name'))) {
        $i = array_search($name,array_column($the_list, 'name'));
        unset($the_list[$i]);
        $the_list = array_values($the_list);
        echo "Done! :)" ;
    } else {
        echo "This item doesn't exist! :(" ;
    }
    return $the_list;
}


// search function
function search($the_list ,$field_name ,$item)
{

    $filteredArray = 
    array_filter($the_list, function($element) use($field_name , $item){
    return isset($element[$field_name]) && str_contains($element[$field_name], $item);
    });

    

    if ($filteredArray == null) {
        return null ;
    } else {
        return $filteredArray ;
    }
}






// main program
$the_list = [] ;

$fields = ['name' , 'number' , 'city' , 'type1' , 'type2' , 'email'];

// phase 1  : if that damn json file even exist at all :|
// phase 2  : loading all the data from existing json to a list
$filename = 'data.json' ;
if (file_exists($filename)){
    $json = file_get_contents('data.json');
    $the_list = json_decode($json, true);
}

// phase 3  : input the first word to identify the command
$command = $argv[1] ;

// commands list : - insert - update - list - search - get - clear
// phase 4  : get the inputs as the command and call the functions
switch($command){

    // names should be specific 
    case "insert": // insert hamed +98 tehran moblie family email
        $name = $argv[2] ;
        $number = $argv[3] ;
        $city = $argv[4] ;
        $type1 = $argv[5] ;
        $type2 = $argv[6] ;
        $email = $argv[7] ;
        $the_list = insert ($the_list , $name , $number , $city , $type1 , $type2 , $email) ;

        break;
    
    // updates happen by name
    case "update": // update name field_name new_info
        $name = $argv[2] ;
        $field_name = $argv[3] ;
        $new_info = $argv[4] ;
        while (!in_array($field_name,$fields)) {
            echo "the field you entered does not exist. please try again.\n" ;
            $field_name = readline();
        }
        $the_list = update ($the_list , $name , $field_name , $new_info) ;
        break;
    
    // list (list by names and numbers)
    case "list": 
        if ($the_list == null) {
            echo "the list is empty!" ;
        } else {
            echo "name\tnumber\n";
            for ($i = 0 ; $i < count($the_list) ; $i++) {
                echo $the_list[$i]['name'] . "\t" . $the_list[$i]['number'] ;
            }    
        }
        break;
    
    // list field_name (list by names and numbers and feild_name)
    case "listby" :
        $field_name = $argv[2] ;
        while (!in_array($field_name,$fields)){
            echo "the field you entered does not exist. please try again.\n" ;
            $field_name = readline();
        }
        if ($the_list == null) {
            echo "the list is empty!" ;
        } else {
            echo "name\tnumber\t".$field_name."\n";
            for ($i = 0 ; $i < count($the_list) ; $i++){
                echo $the_list[$i]['name'] . "\t" . $the_list[$i]['number']  . "\t" . $the_list[$i][$field_name];
            }    
        }  
        break;
    
    // search item
    case "search":
        $item = $argv[2] ;
        $flag = 0 ;
        foreach ($fields as $x){
            $result  = search($the_list , $x ,  $item );
            if ($result != null){
                usort($result, function($a,$b) use ($x , $item){
                return levenshtein($item, $a[$x]) <=> levenshtein($item, $b[$x]);
                });
                $flag = 1 ;
                foreach ($result as $element){
                    echo implode(" " , $element) . "\n";
                }
            }
        }
        
        

        if ($flag == 0) {
            echo "This item doesn't exist! :(";
        }
        break;
    
    // get field_name item
    case "get":
        $field_name = $argv[2] ;
        $item = $argv[3] ;
        while (!in_array($field_name,$fields)){
            echo "the field you entered does not exist. please try again.\n" ;
            $field_name = readline();
        }
        $result  = get($the_list , $field_name ,  $item );
        if ($result != null) {
            foreach ($result as $element){
                echo implode(" " , $element) . "\n";
            }
        }
        
        break;
    
    // clear name
    case "clear":
        $name = $argv[2] ;
        $the_list = clear ($the_list , $name ) ;

        break;
    
    // none of the commands that are defined
    default:
        echo "The shitty command you wrote, doesn't exist :| " ;
        break;
}


// phase 5  : save the datas to a json file
$json = json_encode($the_list);
file_put_contents('data.json', $json);

// phase 6  : the end ! :)

?>
