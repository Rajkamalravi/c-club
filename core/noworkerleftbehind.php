<?php
include_once(TAOH_PLUGIN_PATH.'/form_json.php');
$fields = json_decode($result, true);
//print_r($fields);
foreach($fields as $key => $value) {
    if($value['type'] == 'text') {
        echo '<div class="form-group">';
        echo '<label for="'.$value['name'].'">'.$value['label'].'</label>';
        echo '<input type="text" class="form-control" id="'.$value['name'].'" name="'.$value['name'].'" value="'.$value['value'].'">';
        echo '</div>';
    }
    if($value['type'] == 'textarea') {
        echo '<div class="form-group">';
        echo '<label for="'.$value['name'].'">'.$value['label'].'</label>';
        echo '<textarea class="form-control" id="'.$value['name'].'" name="'.$value['name'].'">'.$value['value'].'</textarea>';
        echo '</div>';
    }
    if($value['type'] == 'select') {
        echo '<div class="form-group">';
        echo '<label for="'.$value['name'].'">'.$value['label'].'</label>';
        echo '<select class="form-control" id="'.$value['name'].'" name="'.$value['name'].'">';
        foreach($value['options'] as $option) {
            echo '<option value="'.$option['value'].'"';
            if($option['value'] == $value['value']) {
                echo ' selected';
            }
            echo '>'.$option['label'].'</option>';
        }
        echo '</select>';
        echo '</div>';
    }
    if($value['type'] == 'radio') {
        echo '<div class="form-group">';
        echo '<label for="'.$value['name'].'">'.$value['label'].'</label>';
        foreach($value['options'] as $option) {
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="radio" name="'.$value['name'].'" id="'.$value['name'].$option['value'].'" value="'.$option['value'].'"';
            if($option['value'] == $value['value']) {
                echo ' checked';
            }
            echo '>';
            echo '<label class="form-check-label" for="'.$value['name'].$option['value'].'">'.$option['label'].'</label>';
            echo '</div>';
        }   
        echo '</div>';  
    }         
}
?>