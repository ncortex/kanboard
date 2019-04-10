<?php

namespace Kanboard\Plugin\MetaMagik\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\UserModel;

/**
 * Meta helper
 *
 */
class MetaHelper extends Base
{

    public function renderMetaTextField($key, $value, array $errors = array(), array $attributes = array())
    {
        $html = "";
        $html .= $this->helper->form->label($key, 'metamagikkey_' . $key);
        $html .= $this->helper->form->text('metamagikkey_' . $key, ['metamagikkey_' . $key => $value], $errors, $attributes, 'form-input-small');
        return $html;
    }

    public function renderMetaNumberField($key, $value, array $errors = array(), array $attributes = array())
    {
        $html = "";
        $html .= $this->helper->form->label($key, 'metamagikkey_' . $key);
        $html .= $this->helper->form->number('metamagikkey_' . $key, ['metamagikkey_' . $key => $value], $errors, $attributes, 'form-input-small');
        return $html;
    }

    public function renderMetaListField($key, $value, array $list, $type, array $errors = array(), array $attributes = array())
    {
        $map_list = [];
        foreach ($list as $name => $value) {
            $map_list[$value] = $value;
        }

        $html = "";
        $html .= $this->helper->form->label($key, 'metamagikkey_' . $key);

        switch ($type){
            case "radio": $html .= $this->helper->form->radios('metamagikkey_' . $key, $map_list, ['metamagikkey_' . $key => $value]); break;
            case "list": $html .= $this->helper->form->select('metamagikkey_' . $key, $map_list, ['metamagikkey_' . $key => $value], $errors, $attributes, 'form-input-small'); break;
            case "check": $html .= $this->helper->form->checkboxes('metamagikkey_' . $key, $map_list, ['metamagikkey_' . $key => $value]); break;
        }

        return $html;
    }

    public function renderMetaUsersField($key, $value, array $errors = array(), array $attributes = array()){
        $aux_user = new UserModel($this->container);
        $users_table = $aux_user->getActiveUsersList(false);
        $users = [];
        foreach ($users_table as $name => $valuex) {
            $users[] = $valuex;
        }
        return $this->renderMetaListField($key, $value, $users, 'list', $errors, $attributes);
    }

    public function renderMetaTableField($key, $value, $table_name, $keycolumn, $valuecolumn, array $errors = array(), array $attributes = array()){
        $meta_opt[''] = '';
        $aux_table = $this->db->table($table_name)->columns($keycolumn, $valuecolumn)->findAll();
        foreach ($aux_table as $valuex) {
            $meta_opt[$valuex[$keycolumn]] = $valuex[$valuecolumn];
        }
        return $this->renderMetaListField($key, $value, $meta_opt, 'list', $errors, $attributes);
    }

    public function renderMetaFields(array $values, $column_number, array $errors = array(), array $attributes = array())
    {
        $metasettings = $this->metadataTypeModel->getAllInColumn($column_number);
        $metadata = $this->taskMetadataModel->getAll($values['id']);
        $html = '';

        if (isset($values['id'])) {
            foreach ($metasettings as $setting) {
                if ($setting['attached_to'] == 'task') {
                    $metaisset = $this->taskMetadataModel->exists($values['id'], $setting['human_name']);
                    if (!$metaisset) {
                        $this->taskMetadataModel->save($values['id'], [$setting['human_name'] => '']);
                    }
                }
            }
        }

        foreach ($metasettings as $setting) {
            $key = $setting['human_name'];
            $values['metamagikkey_' . $key] = $metadata[$key];
            $opt_explode = explode(',', $setting['options']);
            $new_attributes = $attributes;
            if($setting['is_required']) {
                $new_attributes['required'] = "required";
            }

            switch ($setting['data_type']){
                case "text": $html .= $this->renderMetaTextField($key, $metadata[$key] ? $metadata[$key] : "", $errors, $new_attributes); break;
                case "number": $html .= $this->renderMetaNumberField($key, $metadata[$key] ? $metadata[$key] : "", $errors, $new_attributes); break;
                case "table": $html .= $this->renderMetaTableField($key, $metadata[$key] ? $metadata[$key] : "", $opt_explode[0], $opt_explode[1], $opt_explode[2], $errors, $new_attributes); break;
                case "users": $html .= $this->renderMetaUsersField($key, $metadata[$key] ? $metadata[$key] : "", $errors, $new_attributes); break;
                case "list": $html .= $this->renderMetaListField($key, $metadata[$key] ? $metadata[$key] : "", $opt_explode, $setting['data_type'], $errors, $new_attributes); break;
            }
        }

        return $html;
    }

}
