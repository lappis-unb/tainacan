<?php
/*
 *
 * View responsavel em listar todas propriedades do objeto em questao, utilizada para pegar os valores para edicao dos eventos
 */
include_once ('js/show_list_event_properties_js.php');
include_once('./../../helpers/view_helper.php');
include_once('./../../helpers/object/single_properties_widgets_helper.php');
include_once('./../../helpers/object/object_properties_widgets_helper.php');
$objectHelper = new ObjectSingleWidgetsHelper();
$properties_autocomplete = [];
$properties_terms_radio = [];
$properties_terms_tree = [];
$properties_terms_selectbox = [];
$properties_terms_checkbox = [];
$properties_terms_multipleselect = [];
$properties_terms_treecheckbox = [];
//referencias
$references = [
    'properties_autocomplete' => &$properties_autocomplete,
    'properties_terms_radio' => &$properties_terms_radio,
    'properties_terms_checkbox' => &$properties_terms_checkbox,
    'properties_terms_tree' => &$properties_terms_tree,
    'properties_terms_selectbox' => &$properties_terms_selectbox,
    'properties_terms_multipleselect' => &$properties_terms_multipleselect,
    'properties_terms_treecheckbox' => &$properties_terms_treecheckbox   
];
$ids = [];

 if (!isset($property_object) && !isset($property_data)  && !isset($property_term)): ?>
        <center><h4><?php _e('No Properties available', 'tainacan'); ?></h4></center>
<?php endif; ?>
    <input type="hidden" name="properties_object_ids" id='properties_object_ids' value="<?php echo implode(',', $ids); ?>">
<?php if (isset($property_object)):
    foreach ($property_object as $property) {
        if(!$objectHelper->is_public_property($property))
            continue;
        $object_id = $property['metas']['object_id'];
        $ids[] = $property['id'];
    ?>
        <div class="col-md-6 property-root no-padding">
            <div class="box-item-paddings">
                <h4 class="title-pipe single-title"> <?php echo $property['name']; ?> </h4>
                <?php
                if($property['type'] != "user")
                {
                    ?>
                    <div class="edit-field-btn">
                        <button type="button"
                                onclick="cancel_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')"
                                id="single_cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                                class="btn btn-default btn-xs" style="display: none;" >
                            <span class="glyphicon glyphicon-arrow-left" ></span>
                        </button>
                        <?php
                        // verifico se o metadado pode ser alterado
                        if((get_current_user_id() == 0 && verify_anonimous_approval_allowed($collection_id, 'socialdb_collection_permission_edit_property_object_value'))
                            || get_current_user_id() != 0)
                        {
                            if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_object_value', $object_id))
                            {
                                ?>
                                <button type="button" onclick="edit_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" >
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>
                                <?php
                            }
                        }
                        ?>

                        <button type="button" onclick="save_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>

                    </div>
                    <?php
                }
                ?>

                
            <div id="labels_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                <?php if (!empty($property['metas']['objects']) && !empty($property['metas']['value'])) {
                    // percoro todos os objetos 
                    foreach ($property['metas']['objects'] as $object) { 
                        if (isset($property['metas']['value']) && !empty($property['metas']['value']) && in_array($object->ID, $property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao
                            echo '<b><a href="' . get_the_permalink($property['metas']['collection_data'][0]->ID) . '?item=' . $object->post_name . '" >' . $object->post_title . '</a></b><br>';
                        endif;
                    }
                } else {                    
                    // verifico se o metadado pode ser alterado
                    if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_object_value',$object_id)): ?>
                        <button type="button" onclick="edit_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default" >
                            <?php _e('Empty field. Click to edit','tainacan'); ?>
                        </button>                        
                    <?php else:
                        echo '<p>' . __('empty field', 'tainacan') . '</p>';
                    endif;                    
                }
                ?>
            </div>
            <div style="display: none;" id="widget_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                <?php
                //acao para modificaco da propriedade de objeto na insercao do item
                if(has_action('modificate_single_item_properties_object')):
                         do_action('modificate_single_item_properties_object',$property);
                endif;
                ?>
                <?php
                    if(has_action('modificate_label_insert_item_properties')):
                        do_action('modificate_label_insert_item_properties', $property);
                    endif;
                ?>

                <input type="text" onkeyup="autocomplete_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" id="single_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" placeholder="<?php _e('Type the three first letters of the object ', 'tainacan'); ?>"  class="chosen-selected form-control" />
                <select onclick="clear_select_object_property(this,'<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');"  id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" multiple class="chosen-selected2 form-control" style="height: auto;" multiple name="socialdb_property_<?php echo $property['id']; ?>[]" id="chosen-selected2-user"  >
                    <?php if (!empty($property['metas']['objects'])) { ?>
                        <?php foreach ($property['metas']['objects'] as $object) { // percoro todos os objetos  ?>
                            <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && in_array($object->ID, $property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao  ?>
                                <option selected='selected' value="<?php echo $object->ID ?>"><?php echo $object->post_title ?></option>
                                <?php endif; ?>
                            <?php } ?>
                        <?php }else { ?>
                        <option value=""><?php _e('No objects added in this collection', 'tainacan'); ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" id="single_property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" name="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" value="<?php if (is_array($property['metas']['value'])) echo implode(',', is_array($property['metas']['value'])); ?>">
            </div>

        </div>
        </div>
    <?php } ?>
<?php
endif;

if (isset($property_data)):
    $counter = 0;

    $meta = get_post_meta($collection_id, 'socialdb_collection_fixed_properties_visibility', true);
	if ($meta && $meta != ''):
		$collectionPropertiesView = explode(',', $meta);
	else:
		$collectionPropertiesView = [];
	endif;

    foreach ($property_data as $property) {
        if(!$objectHelper->is_public_property($property) || in_array($property['id'], $collectionPropertiesView))
            continue;
            
        $object_id = $property['metas']['object_id']; ?>

        <?php
        $tooltip_text =  __('Help: ', 'tainacan');
        if ($property['metas']['socialdb_property_help']) {
            $tooltip_text .= $property['metas']['socialdb_property_help'];
        }
        ?>

        <div class="col-md-6 property-data no-padding">
            <div class="box-item-paddings">
            <h4 class="title-pipe single-title">
                <?php echo $property['name']; ?>
                <span class="help-block" style="display: inline-block; font-size: 12px;">
                    <a href="javascript:void(0)" data-toggle="tooltip" title="<?php echo $tooltip_text ?>" style="color: black">
                        <?php ViewHelper::render_icon("help"); ?>
                    </a>
                </span>
            </h4>
            <?php if($property['type'] != 'user') { ?>
                    <div class="edit-field-btn">
                        <button type="button" onclick="cancel_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                        <?php // verifico se o metadado pode ser alterado
                        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)): ?>
                            <button type="button" onclick="edit_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                        <?php endif; ?>
                        <button type="button" onclick="save_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                    </div>
                    <?php
                }
            ?>

            <!--- Mostra o valor do metadado----->
            <div id="value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
            <?php if($property['metas']['value'] && !empty($property['metas']['value']) &&  is_array($property['metas']['value'])): ?>
                <?php foreach ($property['metas']['value'] as $value){
                    if($property['type'] == 'user')
                    {
                        $user = get_user_by("id", $value);
                        $value = $user->data->display_name;
                    }
                    ?>


                    <p>
                        <?php
                        $is_url = filter_var($value, FILTER_VALIDATE_URL);
                        /*if(!$is_url) { $is_url = filter_var("http://".$value, FILTER_VALIDATE_URL);
                            if(!$is_url) { $is_url = filter_var("http://www.".$value, FILTER_VALIDATE_URL);
                                if($is_url) { $value_href = "http://www.".$value; }
                            } else $value_href = "http://".$value;
                        } else $value_href = $value;*/

                        if ($is_url):
                            echo '<b><a class="can_short" target="_blank" href="' . $value . '" >' . $value . '</a></b>';
                        elseif (filter_var(trim($value), FILTER_VALIDATE_EMAIL)):
                            echo '<b><a class="can_short" target="_blank" href="mailto:' . $value . '">' . $value . '</a></b>';
                        elseif ($value):
                            // echo '<b><a style="cursor:pointer; white-space: pre-wrap;" onclick="wpquery_link_filter(' . "'" . preg_replace('/\s+/', ' ', $value) . "'" . ',' . $property['id'] . ')">' . $value . '</a></b>';
                            echo '<b><a style="cursor:pointer; white-space: pre-wrap;">' . $value . '</a></b>';
                        endif;
                        ?>
                    </p>
                    <?php
                 } ?>
            <?php else: ?>
                
            <?php // verifico se o metadado pode ser alterado
              if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)): ?>
                <button type="button" onclick="edit_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default" >
                    <?php _e('Empty field. Click to edit','tainacan'); ?>
                </button>
            <?php else: ?>
                <?php _e('empty field','tainacan'); ?>
              <?php endif; ?>                    
            <?php endif; ?>
            </div>
            <p>
            <!--- Fim: Mostra o valor do metadado----->
            <!-- Widgets para edicao -->
                <?php

                $object_properties_widgets_helper = new ObjectWidgetsHelper();
                $meta = unserialize(get_post_meta($object_id, 'socialdb_property_helper_' . $property['id'], true));
                $indexed_properties = [];
                if($meta && !empty($meta) && is_array($meta))
                {
                    foreach ($meta as $property_index => $property_helper) {
                        foreach ($property_helper as $atom) {
                            $type = $atom['type'];
                            $values = $atom['values'];

                            foreach ($values as $value) {
                                $meta_value = $object_properties_widgets_helper->sdb_get_post_meta($value);
                                //$indexed_properties[$meta_value->meta_id] = $meta_value->meta_value;
                                if(isset($meta_value->meta_value))
                                {
                                    $indexed_properties[$meta_value->meta_id] = $meta_value->meta_value;
                                }
                            }
                        }
                    }
                }


                if(empty($indexed_properties))
                {
                    $indexed_properties[] = '';
                }

                if(has_action('modificate_single_item_properties_data')){
                   do_action('modificate_single_item_properties_data',$property,$object_id);
                }else if ($property['type'] === 'text')
                {
                    foreach($indexed_properties as $index => $value)
                    {
                          ?>
                          <input id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_<?php echo $index?>" style="display: none; margin: 7px 0px 7px 0px;" disabled="disabled" value="<?php if ($value) echo $value ?>" type="text" class="form-control"
                                 name="socialdb_property_<?php echo $property['id']; ?>"
                                 data-index="<?php echo $index; ?>"
                                  <?php
                                  if (!$property['metas']['socialdb_property_required']):
                                      echo 'required="required"';
                                  endif;
                                  ?>
                          >
                          <?php
                    }
                } elseif ($property['type'] === 'textarea')
                {
                    foreach($indexed_properties as $index => $value)
                    {
                        ?>
                        <textarea disabled="disabled"
                                  style="display: none;margin: 7px 0px 7px 0px;"
                                  id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                                  class="form-control" name="socialdb_property_<?php echo $property['id']; ?>"
                                  data-index="<?php echo $index; ?>"
                                <?php
                                    if (!$property['metas']['socialdb_property_required']):
                                        echo 'required="required"';
                                    endif;
                                ?> ><?php
                            if ($value)
                                echo $value;
                            ?></textarea>
                        <?php
                    }
                }elseif ($property['type'] === 'date' && !has_action('modificate_single_item_properties_data'))
                {
                    foreach($indexed_properties as $index => $value)
                    {
                        ?>
                        <input style="display: none;"
                               disabled="disabled"
                               value="<?php if ($value) echo $value ?>"
                               id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                               type="text" class="form-control input_date"
                               name="socialdb_property_<?php echo $property['id']; ?>"
                               data-index="<?php echo $index; ?>"
                        >
                        <?php
                    }
                }
                else
                {
                    foreach($indexed_properties as $index => $value)
                    {
                        ?>
                        <input style="display: none;" disabled="disabled"
                               value="<?php if ($value) echo $value ?>"
                               id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                               type="text" class="form-control"
                               data-index="<?php echo $index; ?>"
                               name="socialdb_property_<?php echo $property['id']; ?>"
                            <?php
                                if (!$property['metas']['socialdb_property_required']):
                                    echo 'required="required"';
                                endif;
                            ?>
                        >
                        <?php
                    }
                }

                if(isset($property['metas']["socialdb_property_data_cardinality"]) && $property['metas']["socialdb_property_data_cardinality"]=='n'):
                ?>
                <div id="area_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" style="display: none;">
                    <div id="new_fields_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"></div>
                    <button onclick="showNewField(<?php echo $property['id']; ?>,<?php echo $object_id; ?>,'<?php echo $property['type'] ; ?>')" class="btn btn-primary"><?php _e('Add new field','tainacan') ?></button>
                </div>
                <?php
                endif;
                ?>

                <!-- arrumar num -->
                <input style="display: none;" type="hidden" id="single_property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" name="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" value="<?php if (is_array($property['metas']['value'])) echo implode(',', $property['metas']['value']); ?>">
            </p>

        </div>
        </div>
    <?php }
endif;

if (isset($property_term)): ?>
    <!--h4> <?php _e('Term properties', 'tainacan'); ?></h4-->
    <?php foreach ($property_term as $property) {
        if(!$objectHelper->is_public_property($property))
            continue;
       // if (count($property['has_children']) > 0):?>
            <div class="col-md-6 property-term no-padding">
                <div class="box-item-paddings">
                    <h4 class="title-pipe single-title"> <?php echo $property['name']; ?></h4>
                    <?php
                    if($property['type'] != 'user')
                    {
                        ?>
                        <div class="edit-field-btn">
                            <?php
                                if(!$property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_term_cardinality'] == 1
                                    && (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_classification',$object_id) || current_user_can( "manage_options", $object_id )))
                                {
                                    $category_id = end(get_post_meta($property['metas']['object_id'], 'socialdb_property_'.$property['id'].'_cat'));
                                    ?>
                                    <!--button type="button"
                                                onclick="remove_classication('<?php _e('Remove classification', 'tainacan') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', <?= $category_id ?>, <?= $object_id ?>, '<?php echo mktime(); ?>');"
                                            id="single_remove_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                                            class="btn btn-default btn-xs" >
                                        <span class="glyphicon glyphicon glyphicon-remove" ></span>
                                    </button-->
                                    <?php
                                }
                            ?>
                            <button type="button" onclick="cancel_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" >
                                <span class="glyphicon glyphicon-arrow-left" ></span>
                            </button>
                            <?php
                            // verifico se o metadado pode ser alterado
                            if (verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification',$object_id)): ?>
                                <button type="button" onclick="edit_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" >
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    ?>
                        <!--button type="button" onclick="cancel_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                        <button type="button" onclick="edit_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button-->
                        <!--button type="button" onclick="save_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-floppy-disk"></span></button-->
                    <p> <?php if ($property['metas']['socialdb_property_help']) {
                            echo $property['metas']['socialdb_property_help'];
                        } ?>
                    </p>


                    <div id="labels_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                    <?php
                        $meta = get_post_meta($object_id, 'socialdb_property_helper_' . $property['id'], true);
                        $objectHelper->getValuesViewSingleMedia($meta,$property['id'],$object_id,$collection_id);
                    ?>
                    </div>
                    <!-- Edição de metadado -->
                    <div style="display:none;" id="widget_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                        <?php
                        if ($property['type'] == 'radio') {
                            $properties_terms_radio[] = $property['id'];
                            ?>
                            <div id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                            <input type="hidden" value="" name="value_radio_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_single_radio_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                            <?php
                        } elseif ($property['type'] == 'tree') {
                            $properties_terms_tree[] = $property['id'];
                            ?>
                            <div class="row">
                                <div class='col-lg-12'  id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></div>
                                <!--select name='socialdb_propertyterm_<?php echo $property['id']; ?>' size='2' class='col-lg-6' id='socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select-->
                                <input type="hidden" value="" name="value_tree_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_single_tree_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                            </div>
                            <?php
                        } elseif ($property['type'] == 'selectbox') {
                            $properties_terms_selectbox[] = $property['id'];
                            ?>
                            <select onchange="get_event_single_select(this,<?php echo $property['id']; ?>,<?php echo $object_id; ?>)" class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                            <input type="hidden" value="" name="value_select_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_single_select_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                            <?php
                        } elseif ($property['type'] == 'checkbox') {
                            $properties_terms_checkbox[] = $property['id'];
                            ?>
                            <div id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                            <?php
                        } elseif ($property['type'] == 'multipleselect') {
                            $properties_terms_multipleselect[] = $property['id'];
                            ?>
                            <select  multiple class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                            <?php
                        } elseif ($property['type'] == 'tree_checkbox') {
                            $properties_terms_treecheckbox[] = $property['id'];
                            ?>
                            <div class="row">
                                <div class='col-lg-12'  id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                                <!--select onclick="remove_classication('<?php _e('Remove classification') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', $(this).val()[0],<?php echo $object_id; ?>, '<?php echo mktime(); ?>')" multiple size='6' class='col-lg-6' name='socialdb_propertyterm_<?php echo $property['id']; ?>[]' id='socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select-->
                            </div>
                        <?php }
                        ?>
                    </div>
                </div>
            </div>
                <?php
           // endif;
        }
    endif;
    
if(isset($property_compounds)):
    $objectHelper->list_properties_compounds($property_compounds, $object_id, $references);
endif;
    ?>
    <input type="hidden" id="delete-classification" value="<?php echo verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_classification',$object_id) ?>">
    <input type="hidden" name="categories_id" id='event_single_object_categories_id_<?php echo $object_id; ?>' value="<?php echo implode(',', $categories_id); ?>">
    <input type="hidden" name="properties_terms_radio" id='event_single_properties_terms_radio' value="<?php echo implode(',', array_unique($properties_terms_radio)); ?>">
    <input type="hidden" name="properties_terms_tree" id='event_single_properties_terms_tree' value="<?php echo implode(',', array_unique($properties_terms_tree)); ?>">
    <input type="hidden" name="properties_terms_selectbox" id='event_single_properties_terms_selectbox' value="<?php echo implode(',', array_unique($properties_terms_selectbox)); ?>">
    <input type="hidden" name="properties_terms_checkbox" id='event_single_properties_terms_checkbox' value="<?php echo implode(',', array_unique($properties_terms_checkbox)); ?>">
    <input type="hidden" name="properties_terms_multipleselect" id='event_single_properties_terms_multipleselect' value="<?php echo implode(',', array_unique($properties_terms_multipleselect)); ?>">
    <input type="hidden" name="properties_terms_treecheckbox" id='event_single_properties_terms_treecheckbox' value="<?php echo implode(',', array_unique($properties_terms_treecheckbox)); ?>">
    <input type="hidden" id="object_classifications_event_single_<?php echo $object_id; ?>" name="object_classifications" value="<?php echo implode(',', $categories_id); ?>">

    <?php if (isset($all_ids)): ?>
        <input type="hidden" name="properties_id" value="<?php echo $all_ids; ?>">
        <?php
    endif;