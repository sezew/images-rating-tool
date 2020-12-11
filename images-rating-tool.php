<?php
/*
 * Plugin Name: Images Rating Tool
 * Description: Плагин сравнения и оценки изображений. Вывод конкурса [page_rating slug="конкурс-2"]
 * Author URI:  https://xn--80akhmlofgv3i.xn--p1ai/
 * Plugin URI:  https://github.com/sezew/images-rating-tool
 * Author:      Igor Sezev
 * Version:     1.0
*/

add_action('admin_init', 'spp_plugin_has_parents');
function spp_plugin_has_parents()
{
    if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('advanced-custom-fields/acf.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php'))
    {

        add_action('admin_notices', 'spp_plugin_notice');

        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate']))
        {
            unset($_GET['activate']);
        }
    }
}
function spp_plugin_notice()
{
    ?>
        <div class="error">
        <p>Для использования плагина "Images Rating Tool" установите и активируйте плагин "Advanced Custom Fields"</p>
        </div>
    <?php
}

add_action('init', 'create_taxonomy');
function create_taxonomy()
{
    register_taxonomy('taxonomy_works', ['post_type_work'], [
        'label' => '',
        'labels' => [
            'name' => 'Конкурсы',
            'singular_name' => 'Конкурс',
            'search_items' => 'Найти конкурсы',
            'all_items' => 'Все конкурсы',
            'view_item ' => 'Смотреть конкурс',
            'parent_item' => 'Родительский конкурс',
            'parent_item_colon' => 'Родительский конкурс:',
            'edit_item' => 'Редактировать конкурс',
            'update_item' => 'Обновить конкурс',
            'add_new_item' => 'Добавить конкурс',
            'new_item_name' => 'Новое название коркурса',
            'menu_name' => 'Конкурсы',
        ],
        'description' => '',
        'public' => true,
        // 'publicly_queryable'    => null,
        // 'show_in_nav_menus'     => true,
        // 'show_ui'               => true,
        // 'show_in_menu'          => true,
        // 'show_tagcloud'         => true,
        // 'show_in_quick_edit'    => null,
        'hierarchical' => false,
        'rewrite' => true,
        //'query_var'             => $taxonomy,
        'capabilities' => array() ,
        'meta_box_cb' => null,
        'show_admin_column' => true,
        'show_in_rest' => null,
        'rest_base' => null,
        // '_builtin' => false,
        //'update_count_callback' => '_update_post_term_count',
    ]);
}

add_action('init', 'register_post_types');
function register_post_types()
{
    register_post_type('post_type_work', [
        'label' => null,
        'labels' => [
            'name' => 'Работы',
            'singular_name' => 'Работа',
            'add_new' => 'Добавить работу',
            'add_new_item' => 'Добавление работы',
            'edit_item' => 'Редактирование работы',
            'new_item' => 'Новая работа',
            'view_item' => 'Смотреть работу',
            'search_items' => 'Искать работу',
            'not_found' => 'Не найдено',
            'not_found_in_trash' => 'Не найдено в корзине',
            'parent_item_colon' => '',
            'menu_name' => 'Работы',
        ],
        'description' => '',
        'public' => true,
        // 'publicly_queryable'  => null,
        // 'exclude_from_search' => null,
        // 'show_ui'             => null,
        // 'show_in_nav_menus'   => null,
        'show_in_menu' => null,
        // 'show_in_admin_bar'   => null,
        'show_in_rest' => null,
        'rest_base' => null,
        'menu_position' => null,
        'menu_icon' => null,
        //'capability_type'   => 'post',
        //'capabilities'      => 'post',
        //'map_meta_cap'      => null,
        'hierarchical' => false,
        'supports' => ['title'],
        'taxonomies' => ['taxonomy_works'],
        'has_archive' => false,
        'rewrite' => true,
        'query_var' => true,
    ]);
}

if (function_exists('acf_add_local_field_group')):
    acf_add_local_field_group(array(
        'key' => 'group_1',
        'title' => 'Параметры работы',
        'fields' => array(
            array(
                'key' => 'field_1000',
                'label' => 'Изображение',
                'name' => 'image_work',
                'type' => 'image',
                'return_format' => 'url',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'default_value' => '',
            ) ,
            array(
                'key' => 'field_1001',
                'label' => 'Количество голосов',
                'name' => 'like_count',
                'type' => 'number',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '0',
                'max' => '',
                'step' => '1',
                'readonly' => '1',
                'default_value' => '0',
            )
        ) ,
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post_type_work',
                ) ,
            ) ,
        ) ,
    ));

endif;

add_filter('manage_edit-post_type_work_columns', 'my_columns');
function my_columns($columns)
{
    $columns['post_type_work_like'] = 'Голосов';
    return $columns;
}

add_action('manage_posts_custom_column', 'populate_columns');
function populate_columns($column)
{
    if ('post_type_work_like' == $column)
    {
        $work_like = esc_html(get_post_meta(get_the_ID() , 'like_count', true));
        echo $work_like;
    }
}

add_filter('manage_edit-post_type_work_sortable_columns', 'sort_me');
function sort_me($columns)
{
    $columns['post_type_work_like'] = 'post_type_work_like';
    return $columns;
}

add_filter('request', 'column_orderby');
function column_orderby($vars)
{
    if (!is_admin()) return $vars;
    if (isset($vars['orderby']) && 'post_type_work_like' == $vars['orderby'])
    {
        $vars = array_merge($vars, array(
            'meta_key' => 'like_count',
            'orderby' => 'meta_value_num'
        ));
    }
    return $vars;
}

add_action('wp_enqueue_scripts', 'plugin_files');
function plugin_files()
{
    wp_enqueue_style('rating-style', plugins_url('/css/style.css', __FILE__) , false);
    wp_enqueue_style('lightbox-style', plugins_url('/lightbox2/lightbox.min.css', __FILE__) , false);
    wp_enqueue_script('lightbox-script', plugins_url('/lightbox2/lightbox.min.js', __FILE__) , array('jquery') , null, true);
    wp_enqueue_script('rating-script', plugins_url('/js/script.js', __FILE__) , array() , null, true);
}

add_shortcode('page_rating', 'show_page_rating');
function show_page_rating($atts, $shortcode_content = null)
{
    $html = '';
    if (!empty($atts['slug']))
    {

        $works = get_posts(array(
            'post_type' => 'post_type_work',
            'numberposts' => - 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'taxonomy_works',
                    'field' => 'slug',
                    'terms' => $atts['slug']
                )
            )
        ));

        $term = get_term_by('slug', $atts['slug'], 'taxonomy_works');
        $name = $term->name;

        $arr = '';
        $ids = '';
        foreach ($works as $work)
        {
            $arr .= '"' . get_field('image_work', $work->ID) . '", ';
            $ids .= '"' . $work->ID . '", ';
        }
        $out = '<script>var arr = [' . ($arr != '' ? substr($arr, 0, -2) : '') . '], ids = [' . ($ids != '' ? substr($ids, 0, -2) : '') . '], slug = "' . $atts['slug'] . '";</script>';
        echo $out;

        $html = '<h2 class="">' . $name . '</h2>';
        if (!empty($_COOKIE[$atts['slug']]))
            $html .= '<p class="description">Голосование завершено.</p>';
        else
            $html .= ' <p class="description">Делайте выбор пока не закончатся варианты.</p>
                        <div class="rating">
                            <div class="rating__item">
                                <a href="" data-lightbox="image-1">
                                    <div class="rating__img rating--img1"></div>
                                </a>
                                <button class="rating__button" onclick="set_wokr(jQuery(this).attr(\'data-rival\'), jQuery(this).attr(\'data-rival-url\'))">Голосовать за эту работу &uarr;</button>
                            </div>
                            <div class="rating__item">
                                <a href="" data-lightbox="image-2">
                                    <div class="rating__img rating--img2"></div>
                                </a>
                                <button class="rating__button" onclick="set_wokr(jQuery(this).attr(\'data-rival\'), jQuery(this).attr(\'data-rival-url\'))">Голосовать за эту работу &uarr;</button>
                            </div>
                        </div>';
    }
    return $html;
}

add_action('wp_ajax_save_work', 'save_function');
add_action('wp_ajax_nopriv_save_work', 'save_function');
function save_function()
{
    $work = $_POST['work'];
    $slug = $_POST['slug'];
    if (is_numeric($work) && !empty($slug))
    {
        $count = (int)get_field('like_count', $work);
        $count++;
        update_field('like_count', $count, $work);
    }
    setcookie($slug, "none", time() + 60 * 60 * 24 * 365, "/");
    die;
}

