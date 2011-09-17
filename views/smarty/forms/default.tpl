{if !$multiform}
<div class="box-6">
{if $action}
    <form action="{$action}" method="{$method}">
{else}
    <form method="{$method}">
{/if}
{/if}
{if $multiform}
        <input type="hidden" name="form_id[]" value="{$form_id}" />
{else}
        <input type="hidden" name="form_id" value="{$form_id}" />
{/if}
{foreach $fields as $field}
        <label for="{$form_id}_{$field.name}">{$field.label}:</label>
{if $field.type == "select"}
        <select id="{$form_id}_{$field.name}" name="{$field.name}">
{foreach $field.options as $opt}
{if $field.value && $field.value == $opt.value}
            <option value="{$opt.value}" selected="selected">{$opt.label}</option>
{else}
            <option value="{$opt.value}">{$opt.label}</option>
{/if}<br />
{/foreach}
        </select><br />
{else}
{if $field.value}
        <input id="{$form_id}_{$field.name}" type="{$field.type}" name="{$field.name}" value="{$field.value}" />
{else}
        <input id="{$form_id}_{$field.name}" type="{$field.type}" name="{$field.name}" />
{/if}<br />
{/if}
{/foreach}
{if !$multiform}
        <input type="submit" value="Submit"></input>
    </form>
</div>
{/if}