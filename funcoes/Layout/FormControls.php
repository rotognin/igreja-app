<?php

namespace Funcoes\Layout;

use Funcoes\Helpers\HTML;

class FormControls
{
    public static function hidden($name, $value)
    {
        return "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
    }

    public static function input($label, $name, $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $type = $attrs['type'] ?? 'text';
        $class = $attrs['class'] ?? 'form-control';
        $div_class = $attrs['div_class'] ?? '';
        $event = $attrs['event'] ?? '';
        $style = (isset($attrs['style'])) ? 'style=' . $attrs['style'] : '';
        $add_label = (isset($attrs['add_label'])) ? '&nbsp;&nbsp;&nbsp;' . $attrs['add_label'] : '';
        $label_class = (isset($attrs['label_class'])) ? 'class="' . $attrs['label_class'] . '"' : '';
        $prop = $attrs['prop'] ?? '';
        $has_label = $attrs['has_label'] ?? true;

        unset($attrs['id']);
        unset($attrs['type']);
        unset($attrs['class']);
        unset($attrs['div_class']);
        unset($attrs['event']);
        unset($attrs['style']);
        unset($attrs['add_label']);
        unset($attrs['label_class']);
        unset($attrs['prop']);
        unset($attrs['has_label']);

        $label_for = '';

        if ($has_label) {
            $label_for = "<label for='$id' $label_class>$label</label>$add_label";
        }

        $attrs = HTML::attrs($attrs);
        return <<<HTML
        <div class="form-group $div_class">
            $label_for
            <input type="$type" name="$name" value="$value" id="$id" class="$class" $style $attrs $event $prop/>
        </div>
        HTML;
    }

    public static function checkbox($label, $name, $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $type = $attrs['type'] ?? 'checkbox';
        $class = $attrs['class'] ?? 'form-check-input';
        $div_class = $attrs['div_class'] ?? '';
        $event = $attrs['event'] ?? '';
        $style = (isset($attrs['style'])) ? 'style=' . $attrs['style'] : '';
        $checked = $attrs['checked'] ?? '';

        unset($attrs['id']);
        unset($attrs['type']);
        unset($attrs['class']);
        unset($attrs['div_class']);
        unset($attrs['event']);
        unset($attrs['style']);
        unset($attrs['checked']);

        $attrs = HTML::attrs($attrs);
        return <<<HTML
        <div class="form-check $div_class">
            <input type="$type" name="$name" value="$value" id="$id" class="$class" $style $attrs $checked $event/>
            <label for="$id" class="form-check-label">$label</label>
        </div>
        HTML;
    }

    public static function textarea($label, $name, $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $type = $attrs['type'] ?? 'text';
        $class = $attrs['class'] ?? 'form-control form-control-sm';
        $rows = $attrs['rows'] ?? '3';
        $style = (isset($attrs['style'])) ? 'style="' . $attrs['style'] . '"' : '';
        $div_class = $attrs['div_class'] ?? '';

        unset($attrs['id']);
        unset($attrs['type']);
        unset($attrs['class']);
        unset($attrs['rows']);
        unset($attrs['style']);
        unset($attrs['div_class']);

        $attrs = HTML::attrs($attrs);
        return <<<HTML
        <div class="form-group $div_class">
            <label for="$id">$label</label>
            <textarea name="$name" id="$id" class="$class" rows="$rows" $attrs $style>$value</textarea>
        </div>
        HTML;
    }

    public static function switch($label, $name, $value, $checked = false, $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $div_class = $attrs['div_class'] ?? '';

        $emptyLabel = '';
        if (!empty($attrs['emptyLabel'])) {
            $emptyLabel = '<label class="m-0">&nbsp;</label>';
            unset($attrs['emptyLabel']);
        };

        unset($attrs['id']);
        unset($attrs['div_class']);

        $checked = $checked ? 'checked' : '';

        $attrs = HTML::attrs($attrs);
        return <<<HTML
            <div class="form-group $div_class">
                $emptyLabel
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="$name" value="$value" class="custom-control-input" $checked id="$id" $attrs/>
                    <label class="custom-control-label" for="$id">$label</label>
                </div>
            </div>
        HTML;
    }

    public static function select($label, $name, $options = [], $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $class = $attrs['class'] ?? 'form-control';
        $div_class = $attrs['div_class'] ?? '';
        $event = $attrs['event'] ?? '';
        $prop = $attrs['prop'] ?? '';
        $sem_label = $attrs['sem_label'] ?? false;

        unset($attrs['id']);
        unset($attrs['class']);
        unset($attrs['div_class']);
        unset($attrs['event']);
        unset($attrs['prop']);
        unset($attrs['sem_label']);

        $optionsHTML = "";
        foreach ($options as $optValue => $text) {
            if (is_array($text)) {
                $optionsHTML .= "<optgroup label=\"$optValue\">";
                foreach ($text as $optValue => $text) {
                    $selected = $optValue == $value ? 'selected' : '';
                    $optionsHTML .= "<option value=\"$optValue\" $selected>$text</option>";
                }
                $optionsHTML .= "</optgroup>";
            } else {
                $selected = $optValue == $value ? 'selected' : '';
                $optionsHTML .= "<option value=\"$optValue\" $selected>$text</option>";
            }
        }

        $attrs = HTML::attrs($attrs);

        $addLabel = ($sem_label) ? '' : "<label for='{$id}'>{$label}</label>";

        return <<<HTML
        <div class="form-group $div_class " $event>
            $addLabel
            <select name="$name" id="$id" class="$class" $attrs $prop>
                $optionsHTML
            </select>
        </div>
        HTML;
    }

    public static function select2($label, $name, $options = [], $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $class = $attrs['class'] ?? 'form-control';
        $data = $attrs['data'] ?? [];
        $event = $attrs['event'] ?? '';
        $div_class = $attrs['div_class'] ?? '';
        $values = $attrs['values'] ?? [];
        $parent = $attrs['parent'] ?? '';
        $add_class = $attrs['add_class'] ?? '';

        unset($attrs['id']);
        unset($attrs['class']);
        unset($attrs['data']);
        unset($attrs['event']);
        unset($attrs['div_class']);
        unset($attrs['values']);
        unset($attrs['parent']);
        unset($attrs['add_class']);

        $optionsHTML = "";
        foreach ($options as $optValue => $text) {
            if (is_array($text)) {
                $optionsHTML .= "<optgroup label=\"$optValue\">";
                foreach ($text as $optValue => $text) {
                    $selected = $optValue == $value ? 'selected' : '';
                    $optionsHTML .= "<option value=\"$optValue\" $selected>$text</option>";
                }
                $optionsHTML .= "</optgroup>";
            } else {
                if (!empty($values)) {
                    $selected = in_array($optValue, $values) ? 'selected' : '';
                } else {
                    $selected = $optValue == $value ? 'selected' : '';
                }

                $optionsHTML .= "<option value=\"$optValue\" $selected>$text</option>";
            }
        }

        $attrs = HTML::attrs($attrs);
        $data = HTML::dataAttrs($data);

        $addScript = '';
        $addScript = '{';

        if ($parent != '') {
            $addScript .= 'dropdownParent: $("#' . $parent . '")';
        }

        $addScript .= '}';

        return <<<HTML
        <div class="form-group $div_class" $event>
            <label for="$id">$label</label>
            <select name="$name" id="$id" class="$class" $attrs $data>
                $optionsHTML
            </select>
        </div>
        <script>
            $(function() {
                $('#$id').select2($addScript);
            });
        </script>
        HTML;
    }

    public static function select2ajax($label, $name, $options = [], $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $class = $attrs['class'] ?? 'form-control';
        $data = $attrs['data'] ?? [];
        $event = $attrs['event'] ?? '';
        $div_class = $attrs['div_class'] ?? '';
        $values = $attrs['values'] ?? [];
        $parent = $attrs['parent'] ?? '';
        $url = $attrs['url'] ?? '';

        unset($attrs['id']);
        unset($attrs['class']);
        unset($attrs['data']);
        unset($attrs['event']);
        unset($attrs['div_class']);
        unset($attrs['values']);
        unset($attrs['parent']);
        unset($attrs['url']);

        $optionsHTML = "";
        foreach ($options as $optValue => $text) {
            if (is_array($text)) {
                $optionsHTML .= "<optgroup label=\"$optValue\">";
                foreach ($text as $optValue => $text) {
                    $selected = $optValue == $value ? 'selected' : '';
                    $optionsHTML .= "<option value=\"$optValue\" $selected>$text</option>";
                }
                $optionsHTML .= "</optgroup>";
            } else {
                if (!empty($values)) {
                    $selected = in_array($optValue, $values) ? 'selected' : '';
                } else {
                    $selected = $optValue == $value ? 'selected' : '';
                }

                $optionsHTML .= "<option value=\"$optValue\" $selected>$text</option>";
            }
        }

        $attrs = HTML::attrs($attrs);
        $data = HTML::dataAttrs($data);

        $script = '{';
        if ($parent != '') {
            $script .= 'dropdownParent: $("#' . $parent . '")';
        }

        if ($url != '') {
            if ($parent != '') {
                $script .= ', ';
            }

            $script .= 'ajax: {url: "' . $url . '", dataType: "json", processResults: function(data){ return { results: data.results }; } }';
        }

        $script .= '}';

        return <<<HTML
        <div class="form-group $div_class" $event>
            <label for="$id">$label</label>
            <select name="$name" id="$id" class="$class" $attrs $data>
                $optionsHTML
            </select>
        </div>
        <script>
            $(function() {
                $('#$id').select2($script);
            });
        </script>
        HTML;
    }

    public static function dateRange($label, $name, $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $class = $attrs['class'] ?? 'form-control';
        $data = $attrs['data'] ?? [];

        unset($attrs['id']);
        unset($attrs['class']);
        unset($attrs['data']);

        $attrs = HTML::attrs($attrs);
        $data = HTML::dataAttrs($data);

        return <<<HTML
        <div class="form-group">
            <label for="$id">$label</label>
            <input type="text" name="$name" value="$value" id="$id" class="$class" $attrs $data/>
        </div>
        <script>
            $(function() {
                let data = $('#$id').data();
                $('#$id').daterangepicker({
                    timePicker24Hour: true,
                    showDropdowns: data.showDropdowns || true,
                    ...data,
                    locale: {
                        format: (data.timePicker || false) ? 'DD/MM/YYYY HH:mm' : 'DD/MM/YYYY',
                        ...datarangepickerLocale
                    }
                });
            });
        </script>
        HTML;
    }

    public static function date($label, $name, $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $class = $attrs['class'] ?? 'form-control';
        $data = $attrs['data'] ?? [];
        $div_class = $attrs['div_class'] ?? '';

        unset($attrs['id']);
        unset($attrs['class']);
        unset($attrs['data']);
        unset($attrs['div_class']);

        $attrs = HTML::attrs($attrs);
        $data = HTML::dataAttrs($data);

        return <<<HTML
        <div class="form-group $div_class">
            <label for="$id">$label</label>
            <input type="text" name="$name" value="$value" id="$id" class="$class" $attrs $data/>
        </div>
        <script>
            $(function() {
                let data = $('#$id').data();
                $('#$id').daterangepicker({
                    singleDatePicker: true,
                    timePicker24Hour: true,
                    showDropdowns: data.showDropdowns || true,
                    ...data,
                    locale: {
                        format: (data.timePicker || false) ? 'DD/MM/YYYY HH:mm' : 'DD/MM/YYYY',
                        ...datarangepickerLocale
                    }
                });
            });
        </script>
        HTML;
    }

    public static function radio($label, $name, $options = [], $value = "", $attrs = [])
    {
        $id = $attrs['id'] ?? $name;
        $class = $attrs['class'] ?? 'form-check-input';
        $data = $attrs['data'] ?? [];
        $required = $attrs['required'] ?? '';

        unset($attrs['id']);
        unset($attrs['class']);
        unset($attrs['data']);
        unset($attrs['required']);

        $attrs = HTML::attrs($attrs);
        $data = HTML::dataAttrs($data);

        $optionsHTML = "";
        foreach ($options as $optValue => $text) {
            $checked = $optValue == $value ? 'checked' : '';
            $optionsHTML .= "<div class=\"form-check form-check-inline\">
                <input type=\"radio\" id=\"$id-$optValue\" name=\"$name\" class=\"$class\" value=\"$optValue\" $checked $attrs $required>
                <label class=\"form-check-label\" for=\"$id-$optValue\">$text</label>
            </div>";

            $required = '';
        }

        return <<<HTML
        <div class="form-group" $data>
            <label for="$id">$label</label><br>
            $optionsHTML
        </div>
        HTML;
    }
}
