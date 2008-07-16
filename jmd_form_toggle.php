<?php
$plugin = array(
    'description' => 'Toggleable admin-forms.',
    'version' => '0.1',
    'type' => 1,
);
if (!defined('txpinterface')) include '../zem_tpl.php';

if (0) {
?>

# --- BEGIN PLUGIN HELP ---

Thanks to "Steve Dickinson":http://txp-plugins.netcarving.com/ for making this work.

# --- END PLUGIN HELP ---

<?php
}

# --- BEGIN PLUGIN CODE ---

if (txpinterface === 'admin')
{
    if (gps('event') === 'form')
    {
        ob_start('jmd_form_toggle_head');
        ob_start('jmd_form_toggle');
    }
}

/**
 * Adds headers for each form type.
 *
 * @param string $buffer
 */
function jmd_form_toggle($buffer)
{
    global $DB, $essential_forms, $step;
    if (empty($DB))
    {
        $DB = new DB();
    }
    $out = sLink('form', 'form_create', gTxt('create_new_form'), 'action');
    $rs = safe_rows('name, type', 'txp_form',
        'name !="" order by type, name asc');
    foreach ($rs as $form)
    {
        $forms[$form['type']][] = $form['name'];
    }

    $types = array_keys($forms);
    foreach ($types as $type)
    {
        $out .= <<<EOD
<h3 onclick="jmd_form_toggle('type_{$type}');">{$type}</h3>
<table id="type_{$type}">
EOD;
        for ($i = 0; $i < count($forms[$type]); $i++)
        {
            $out .= '<tr>';
            $formName = $forms[$type][$i];
            if ($curname == $formName)
            {
                $out .= '<td colspan="2">' . $formName;
            }
            else
            {
                $out .= '<td> ' . eLink('form', 'form_edit', 'name',
                    $formName, $formName);
            }
            $out .= '</td>';
            if (!in_array($formName, $essential_forms))
            {
                $out .= '<td class="jmd_form_toggle_checkbox"><input type="checkbox" name="selected_forms[]"
                    value="' . $formName . '"/></td>';
            }
            $out .= '</tr>';
        }
        $out .= '</table>';
    }
    $out .= '<input type="hidden" name="event" value="form" />';

    $pattern = '/<table cellpadding="0" cellspacing="0" border="0" id="list" align="center">(.*)<input type="hidden" name="event" value="form" \/>/s';

    return preg_replace($pattern, tag($out, 'div', ' id="jmd_form_toggle"'),
        $buffer);
}

/**
 * Inserts CSS and JS into the head.
 *
 * @param string $buffer
 */
function jmd_form_toggle_head($buffer)
{
    $out = <<<EOD
<script type="text/javascript">
/**
 * Toggle an element's display.
 *
 * @param string id
 */
function jmd_form_toggle(id)
{
    var el = document.getElementById(id);
    if (el)
    {
        el.style.display = ((el.style.display != 'none') ? 'none' : '');
    }
};
</script>
<style type="text/css">
#jmd_form_toggle .action
{
    display: block;
    margin: 0 0 0.5em;
}
#jmd_form_toggle h3
{
    cursor: pointer;
    margin: 0;
}
#jmd_form_toggle table
{
    border-collapse: collapse;
    margin: 0 0 1em;
    width: 100%;
}
#jmd_form_toggle td
{
    border-bottom: 1px solid #ddd;
    vertical-align: middle;
}
.jmd_form_toggle_checkbox
{
    text-align: right;
}
</style>
EOD;
    $find = '</head>';

    return str_replace($find, $out . $find, $buffer);
}

# --- END PLUGIN CODE ---
?>
