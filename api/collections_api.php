<?php

abstract class CollectionsApi {

    public function get_collections() {
        $CollectionModel = new CollectionModel;
        return $CollectionModel->get_all_collections();
    }

    public function get_collection($request) {
        $params = $request->get_params();

        $CollectionModel = new CollectionModel;
        return $CollectionModel->get_collection_data($params['id']);
    }

    public function get_collection_items($request) {
        $params = $request->get_params();
        
        //se existir consultas
        if(isset($params['filter']))
            return CollectionsApi::filterByArgs($params);
        
        //caso for uma consulta simples
        $CollectionModel = new CollectionModel;
        $data =  $CollectionModel->get_collection_posts($params['id']);
        if ($data) {
            return new WP_REST_Response( $data, 200 );
        }else{
            return new WP_Error('empty_collection',  __( 'No items inserted or found!', 'tainacan' ), array('status' => 404));
        }
    }

    public function get_collection_item($request) {
        $params = $request->get_params();

        $Result['item'] = get_post($params['post']);
        if (empty($Result['item'])) {
            return new WP_Error('invalid_item_id', 'Invalid Item ID', array('status' => 404));
        }
        $Result['metas'] = get_post_meta($params['post']);
        return $Result;
    }
    
    private function filterByArgs($params){
        $filters = $params['filter'];
        $wpQueryModel = new WPQueryModel();
        $args = $wpQueryModel->queryAPI($filters,$params['id']);
        $loop = new WP_Query($args);
        if ($loop->have_posts()) {
            $data = [];
            while ( $loop->have_posts() ) : $loop->the_post();
                $data[] = [
                    'ID'  => get_the_ID(),
                    'title' => get_the_title(),
                    'created_date' => get_post()->post_date,
                    'content' => get_the_content()
                ];
            endwhile;    
            return new WP_REST_Response( $data, 200 );
        }else{
            return new WP_Error('empty_search',  __( 'No items found with these arguments!', 'tainacan' ), array('status' => 404));
        }
    }

}
