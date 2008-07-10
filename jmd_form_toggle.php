<?php
$plugin = array(
    'version' => '0.1',
    'author' => 'Jon-Michael Deldin',
    'author_uri' => 'http://jmdeldin.com',
    'description' => 'Toggleable forms.',
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
        ob_start('jmd_form_toggle');
    }
}

/**
 * Adds headers for each form type.
 * @param string $buffer
 * @todo clean up
 */
function jmd_form_toggle($buffer)
{
    global $DB, $essential_forms, $step;
    if (empty($DB))
    {
        $DB = new DB();
    }
    $out[] = <<<EOD
<script type="text/javascript">
function jmd_form_toggle(id)
{
    var el = document.getElementById(id);
    if (el)
    {
        el.style.display = ((el.style.display != 'none') ? 'none' : '');
    }
}    
</script>
EOD;
    $out[] = tag(
            sLink('form', 'form_create', gTxt('create_new_form'), 'action'),
            'div', ' style="margin: 0 0 0.5em"');
    $rs = safe_rows('name, type', 'txp_form',
        'name !="" order by type, name asc');
    $forms = array();
    foreach ($rs as $form)
    {
        $forms[$form['type']][] = $form['name'];
    }
    
    $types = array_keys($forms);
    foreach ($types as $type)
    {
        $out[] = <<<EOD
<h3 onclick="jmd_form_toggle('type_{$type}');" style="margin:0.5em 0 0.25em;cursor:pointer">{$type}</h3>
<table id="type_{$type}" style="width:100%; border-collapse: collapse; margin: 0 0 0.5em;">
EOD;
        for ($i = 0; $i < count($forms[$type]); $i++)
        {
            $out[] = '<tr style="border-bottom: 1px solid #ddd;">';
            $formName = $forms[$type][$i];
            if ($curname === $formName)
            {
                $out[] = '<td colspan="2">' . $formName;
            }
            else
            {
                $out[] = '<td> ' . eLink('form', 'form_edit', 'name',
                    $formName, $formName);
            }
            $out[] = '</td>';
            if (!in_array($formName, $essential_forms))
            {
                $out[] = '<td style="text-align: right"><input type="checkbox" name="selected_forms[]"
                    value="' . $formName . '"/></td>';
            }
            $out[] = '</tr>';
        }
        $out[] = '</table>';
    }   
    $out[] = '<input type="hidden" name="event" value="form" />';
    $pattern = '/<table cellpadding="0" cellspacing="0" border="0" id="list" align="center">(.*)<input type="hidden" name="event" value="form" \/>/s';

    return preg_replace($pattern, join('', $out), $buffer);
}

# --- END PLUGIN CODE ---

?>
