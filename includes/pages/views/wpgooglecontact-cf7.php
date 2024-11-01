<?php 

class GoogleContactCF7 extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $data = array();
        
        $data        = $this->table_data();
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        
        $count_forms = wp_count_posts('wpcf7_contact_form');
        $totalItems  = $count_forms->publish;

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));
         
        $this->items = $data;
        $columns = $this->get_columns();
        $this->_column_headers = array($columns);
    }

    public function table_data(){
        $data = array();
        $page         = $this->get_pagenum();
        $page         = $page - 1;
        $start        = $page * 10;

        $args = array(
            'post_type' => 'wpcf7_contact_form',
            'order'    => 'ASC',
            'posts_per_page' => 10,
            'offset' => $start
        );

        $the_query = new WP_Query($args);

        while ($the_query->have_posts()) : $the_query->the_post();
            $form_id = get_the_ID();
            $form_title = get_the_title();
            $link  = "<a class='show-cf7form-field' href='#' data-id='%s'>%s</a>";

            $form_data['ID']  = sprintf($link, $form_id, $form_id);
            $form_data['post_title'] = sprintf($link, $form_id, $form_title);
            $data[] = $form_data;
        endwhile;
        return $data;

    }

    public function get_columns(  ){
        $column = array(
            'ID' => 'Form ID',
            'post_title' => 'Form Name'
        );

        return $column;
    }

    public function column_default($item, $column_name){
        switch( $column_name ) {
            case 'ID':
            case 'post_title':
                return $item[ $column_name ];

            default:
                return 'No Forms';
        }
    }

}
$cf7 = new GoogleContactCF7();
$cf7->prepare_items();
?>
<div class="wptogoo_title">
    <img src="<?php echo WP_GOOGLE_CONTACT_FREE_URL; ?>assets/img/arrow.png">
    <h2 class="title">
        <?php echo esc_html(__('Contact Form 7', 'WPGContacts')); ?>
    </h2>
</div>

<div class="cf7_show_form_list common_scroll_css">
    <form method="post" action="">
        <?php
    $cf7->search_box('Search', 'post_title');
    $cf7->display();
?>
    </form>
</div>

<!-- <form id="google_contact_form_field_list"> -->
<div class="cf7_show_field_list common_scroll_css">

</div>
<!-- </form> -->