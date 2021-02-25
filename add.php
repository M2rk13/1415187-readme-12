<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

$select_content_types_query = 'SELECT * FROM content_types;';

$add_tag_query = "INSERT into hashtags SET tag_name = ?";
$add_post_tag_query = "INSERT into post_tags SET post_id = ?, hashtag_id = ?";

$validation_rules = [
    'text' => [
        'heading' => 'filled',
        'content' => 'filled'
    ],
    'photo' => [
        'heading' => 'filled',
        'photo-url' => 'filled|correctURL|ImageURLContent',
        'photo-file' => 'imgloaded'
    ],
    'link' => [
        'heading' => 'filled',
        'link-url' => 'filled|correctURL'
    ],
    'quote' => [
        'heading' => 'filled',
        'content' => 'filled',
        'quote-author' => 'filled'
    ],
    'video' => [
        'heading' => 'filled',
        'video-url' => 'filled|correctURL|youtubeurl'
    ],
];

$validation_rules = [
    'heading' => ['validateFilled'],
    'content' => ['validateFilled'],
    'link-url' => ['validateFilled', 'validateURL'],
    'photo-file' => ['validateImageFields'],
    'photo-url' => ['validateImageFields'],
    'video-url' => ['validateFilled', 'validateURL', 'check_youtube_url'],
    'quote-author' => ['validateFilled']
];

$field_error_codes = [
    'heading' => 'Заголовок',
    'content' => 'Контент',
    'link-url' => 'Ссылка',
    'photo-url' => 'Ссылка из интернета',
    'video-url' => 'Ссылка YOUTUBE',
    'photo-file' => 'Файл фото',
    'quote-author' => 'Автор'
];

$form_type = 'photo';
$con = db_connect("localhost", "mysql", "mysql", "readme");
$content_types_mysqli = mysqli_query($con, $select_content_types_query);
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
$post_types = array_column($content_types, 'id', 'type_class');

if ((count($_POST) > 0) && isset($_POST['form-type'])){
    $form_type = $_POST['form-type'];
    foreach ($_POST as $field_name => $val) {
        $fields['values'][$form_type][$field_name] = $_POST[$field_name];
        if (isset($validation_rules[$field_name])) {
            $fields['errors'][$form_type][$field_name] = validate($field_name, $validation_rules[$field_name]);
        }
    }
    $fields['errors'][$form_type] = array_filter($fields['errors'][$form_type]);
    if (empty($fields['errors'][$form_type])) {
        switch ($form_type) {
            case 'quote':
                secure_query($con, $add_quote_post_query, 'siss', $_POST['heading'], $post_types[$form_type], $_POST['content'], $_POST['quote-author']);
                $post_id = mysqli_insert_id($con);
                break;
            case 'text':
                secure_query($con, $add_text_post_query, 'sis', $_POST['heading'], $post_types[$form_type], $_POST['content']);
                $post_id = mysqli_insert_id($con);
                break;
            case 'link':
                secure_query($con, $add_link_post_query, 'sis', $_POST['heading'], $post_types[$form_type], $_POST['link-url']);
                $post_id = mysqli_insert_id($con);
                break;
            case 'video':
                secure_query($con, $add_video_post_query, 'siss', $_POST['heading'], $post_types[$form_type], $_POST['content'], $_POST['youtube_url']);
                $post_id = mysqli_insert_id($con);
                break;
            case 'photo':
                if ($_FILES['photo-file']['error'] != 0) {
                    $file_url = $_POST['photo-url'];
                } 
                else {
                    $file_name = $_FILES['photo-file']['name'];
                    $file_path = __DIR__ . '/uploads/';
                    $file_url = '/uploads/' . $file_name;
                    move_uploaded_file($_FILES['photo-file']['tmp_name'], $file_path . $file_name);
                    print("<a href='$file_url'>$file_name</a>");
                }
                secure_query($con, $add_photo_post_query, 'siss', $_POST['heading'], $post_types[$form_type], $_POST['content'], $file_url);
                $post_id = mysqli_insert_id($con);
        }
        $new_tags = array_unique(explode(' ', $_POST['tags']));
        $select_tags_query = "SELECT * FROM hashtags WHERE tag_name in ('".implode("','",$new_tags)."')";
        $tags_mysqli = mysqli_query($con, $select_tags_query);
        $tags = mysqli_fetch_all($tags_mysqli, MYSQLI_ASSOC);
        foreach ($new_tags as $new_tag) {
            $index = array_search($new_tag, array_column($tags, 'tag_name'));
            if ($index !== false) {
                unset($new_tags[$new_tag]);
                $tag_id = $tags[$index]['id'];
            } else {
                secure_query($con, $add_tag_query, 's', $new_tag);
                $tag_id = mysqli_insert_id($con);
            }
            secure_query($con, $add_post_tag_query, 'ii', $post_id, $tag_id);
        }
        $URL = '/post.php?id='.$post_id;
        header("Location: $URL");
    }
}

$page_content = include_template('adding-post.php', [
                                                    'content_types' => $content_types,
                                                    'fields_values' => $fields['values'],
                                                    'fields_errors' => $fields['errors'],
                                                    'field_error_codes' => $field_error_codes,
                                                    'form_type' => $form_type
                                                    ]);

print($page_content);

var_export(empty($_FILES));
var_export($_FILES);
var_export($_POST);