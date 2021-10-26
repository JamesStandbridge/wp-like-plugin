<?php
function my_admin_page_contents()
{
    ?>
    <h1>Like Plugin settings</h1>
    <form method="POST" action="options.php">
    <?php
    settings_fields( 'like-plugin' );
    do_settings_sections( 'like-plugin' );
    submit_button();
    ?>
    </form>
    <?php
}


add_action( 'admin_init', 'my_settings_init' );

function my_settings_init()
{

    add_settings_section(
        'like_plugin_settings_section',
        '',
        'like_plugin_settings_section_template',
        'like-plugin'
    );

    add_settings_field(
        'like_message',
        'Like message',
        'like_message_markup',
        'like-plugin',
        'like_plugin_settings_section'
    );
    register_setting( 'like-plugin', 'like_message' );

    add_settings_field(
        'unlike_message',
        'Unlike message',
        'unlike_message_markup',
        'like-plugin',
        'like_plugin_settings_section'
    );
    register_setting( 'like-plugin', 'unlike_message' );

    add_settings_field(
        'display_counter_if_0',
        'Display counter if equal to 0',
        'display_counter_0_markup',
        'like-plugin',
        'like_plugin_settings_section'
    );
    register_setting( 'like-plugin', 'display_counter_if_0' );

    add_settings_field(
        'counter_label',
        'Counter label',
        'counter_label_markup',
        'like-plugin',
        'like_plugin_settings_section'
    );
    register_setting( 'like-plugin', 'counter_label' );

    add_settings_field(
        'counter_label_plural',
        'Counter label plural',
        'counter_label_plural_markup',
        'like-plugin',
        'like_plugin_settings_section'
    );
    register_setting( 'like-plugin', 'counter_label_plural' );

    add_settings_field(
        'markdown_type',
        'Markdown type',
        'markdown_type_markup',
        'like-plugin',
        'like_plugin_settings_section'
    );
    register_setting( 'like-plugin', 'markdown_type' );
}


function like_plugin_settings_section_template()
{
    echo '';
}

function markdown_type_markup()
{
    $items = ["span", "div", "p"];
    echo "<select id='markdown_type' name='markdown_type'>";
    foreach($items as $item) {
        $selected = get_option('markdown_type') === "$item" ? 'selected="selected"' : '';
        echo "<option $selected value='$item'>$item</option>";
    }
    echo "</select>";
}

function like_message_markup()
{
    ?>
    <input type="text" id="like_message" name="like_message" value="<?php echo get_option( 'like_message' ); ?>">
    <?php
}

function unlike_message_markup()
{
    ?>
    <input type="text" id="unlike_message" name="unlike_message" value="<?php echo get_option( 'unlike_message' ); ?>">
    <?php
}

function display_counter_0_markup()
{
    ?>
      <label class="switch">
        <input
          <?php
          checked("1", get_option('display_counter_if_0'), true);
         ?>
        name='display_counter_if_0'
        id='display_counter_if_0'
        type="checkbox"
        value="1"
        >
        <span class="slider blue"></span>
      </label>
    <?php
}

function counter_label_markup()
{
    ?>
    <input type="text" id="counter_label" name="counter_label" value="<?php echo get_option( 'counter_label' ); ?>">
    <?php
}

function counter_label_plural_markup()
{
    ?>
    <input type="text" id="counter_label_plural" name="counter_label_plural" value="<?php echo get_option( 'counter_label_plural' ); ?>">
    <?php
}
