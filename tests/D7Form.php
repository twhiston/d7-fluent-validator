<?php

//A small extract from a form state

function getDrupalData(){
    return array(
      'submitted' =>
        array(
          'number' => '123',
          'street' => 'fake street',
          'postcode' => 'PA1 7GT',
          'town' => 'plymouth',
          'country' => 'uk',
        ),
      'details' =>
        array(
          'nid' => '3',
          'sid' => null,
          'uid' => '1',
          'page_num' => 1,
          'page_count' => 1,
          'finished' => 0,
        ),
      'submit' => 'Submit',
      'cancel' => 'Use My Original Entry',
      'vfprev' => 'Previous Suggestion',
      'vfnext' => 'Next Suggestion',
      'form_build_id' => 'form-BjDTB_0cZFSgz1yONuyc9hf3u6kGcPcbaIzAvrBraQg',
      'form_token' => '4VRDcyQzzjw1rnk-fTEFt6qiMnCZJOQpcXB2DtJqAO8',
      'form_id' => 'webform_client_form_3',
      'op' => 'Submit',
    );
}

function getDrupalFormState(){
    return array(
      "input" => array (
        'title' => 'Foooo',
        'body' =>
          array (
            'und' =>
              array (
                0 =>
                  array (
                    'summary' => 'shsh',
                    'value' => 'hhssaass',
                    'format' => 'filtered_html',
                  ),
              ),
          ),
        'changed' => '',
        'form_build_id' => 'form-WY1t0Uho-0TK-UV3arfQvMjXrCPyBMyE634Ak02uH5k',
        'form_token' => 'Uc2pz0_lgL6MEdZCKFxeXVQ-SdH7ltvxCX1lqzFXe58',
        'form_id' => 'page_node_form',
        'menu' =>
          array (
            'link_title' => '',
            'description' => '',
            'parent' => 'main-menu:0',
            'weight' => '0',
            'enabled' => NULL,
          ),
        'log' => '',
        'path' =>
          array (
            'pathauto' => '1',
            'alias' => '',
          ),
        'comment' => '1',
        'name' => 'admin',
        'date' => '',
        'status' => '1',
        'additional_settings__active_tab' => '',
        'op' => 'Save',
        'revision' => NULL,
        'promote' => NULL,
        'sticky' => NULL,
      )
    );
}