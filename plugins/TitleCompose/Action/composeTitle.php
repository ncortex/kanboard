<?php

namespace Kanboard\Plugin\TitleCompose\Action;

use Kanboard\Action\Base;
use Kanboard\Model\TaskModel;
use function MongoDB\BSON\toJSON;

class composeTitle extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Compose title from attributes');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(
            TaskModel::EVENT_CREATE_UPDATE ,
        );
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return array(
            'atributos' => t('atributos (separados por comas)'),
        );
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array(
            'task_id',
            'task' => array(
                'column_id',
            ),
        );
    }

    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $title="";
        foreach (explode(',', $this->getParam("atributos")) as $attribute){
            $title .= $data['task'][$attribute]." ";
        }

        $subprod = $this->db->getConnection()->query('SELECT * FROM sub_products WHERE id='.$data['task']['subproduct_id'].' LIMIT 1');
        foreach ($subprod as $sp){
            $subprodname = $sp['title'];
        }
        // Los de rmsoft lo quieren con este formato exacto:
        $title = $subprodname . " - " . $data['task']['project_number'] . " (" . $data['task']['package_number'] .")";

        $metadata = $this->taskMetadataModel->getAll($data['task']['id']);
        $description = "";
        if (!empty($metadata['MT'])) {
            $description = $metadata['New'] . "-" . $metadata['MT'] . "-" . $metadata['Fuzzy'] . "-" . $metadata['100%'];
        } elseif (!empty($metadata['Weighed'])) {
            $description = $metadata['Weighed'];
        }

        $values = array(
            'id' => $data['task']['id'],
            'title' => $title,
            'description' => $description,
        );

        return $this->taskModificationModel->update($values, false);
    }

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        return true;
        foreach (explode(',', $this->getParam("atributos")) as $attribute){
            if(empty($data['task'][$attribute])){
                return false;
            }
        }
        return true;
    }
}
