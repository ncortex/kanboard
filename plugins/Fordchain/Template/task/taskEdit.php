<div class="page-header">
    <h2><?= $this->text->e($project['name']) ?> &gt; <?= t('New task') ?></h2>
</div>
<form method="post" id="formTaskCreation" action="<?= $this->url->href('TaskModificationController', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <div class="task-form-container">
        <div class="task-form-main-column">

            <!--= $this->task->renderDescriptionField($values, $errors) -->
            <?= $this->hook->render('template:task:form:first-column', array('values' => $values, 'errors' => $errors)) ?>
            <div class="extra-fields">
                <div class="extra_field">
                    <?= $this->task->renderCategoryField($categories_list, $values, $errors) ?>
                </div>
                <div class="extra_field">
                    <?= $this->task->renderTagField($project, $tags) ; ?>
                </div>
            </div>
            <!-- = $this->task->renderTitleField($values, $errors) -->
            <input type="hidden" name="title" id="form-title" class="" autofocus="" value="title" tabindex="1" placeholder="Titulo">

        </div>

        <div class="task-form-secondary-column">
            <?= $this->helper->fordchainHelper->renderFordAssigneeField($users_list, $values, $errors, [], "gestor_id", "Project Manager",true) ?>
            <?= $this->helper->fordchainHelper->renderFordAssigneeField($users_list, $values, $errors, [], "translator_id", "Traductor",true) ?>
            <?= $this->helper->fordchainHelper->renderFordAssigneeField($users_list, $values, $errors, [], "reviewer_id", "Revisor",true) ?>
            <div style="display: none;   position: absolute !important;   top: -9999px !important;   left: -9999px !important;"> <?= $this->helper->fordchainHelper->renderFordAssigneeField($users_list, $values, $errors, array(), "owner_id") ?> </div>

            <?= $this->hook->render('template:task:form:second-column', array('values' => $values, 'errors' => $errors, 'users_list' => $users_list)) ?>
        </div>

        <div class="task-form-secondary-column">
            <?= $this->task->renderStartDateField($values, $errors) ?>
            <?= $this->task->renderDueDateField($values, $errors) ?>
            <?= $this->task->renderReferenceField($values, $errors) ?>
            <?= $this->task->renderColorField($values) ?>

            <?= $this->hook->render('template:task:form:third-column', array('values' => $values, 'errors' => $errors)) ?>

        </div>

        <div class="task-form-bottom">

            <?= $this->modal->submitButtons() ?>
        </div>
    </div>
</form>
