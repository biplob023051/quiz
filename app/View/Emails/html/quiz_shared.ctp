<h1><?php echo __('Quiz Shared'); ?></h1>
<h2><?php echo __('User Info : ') ?></h2>
<p><?php echo __('Id: ') . $data['User']['id']; ?></p>
<p><?php echo __('Name: ') . $data['User']['name']; ?></p>
<p><?php echo __('Email: ') . $data['User']['email']; ?></p>
<p><?php echo __('To view, approve or decline sharing, please clik the quiz name or copy paste the bellow link'); ?></p>
<a href="<?php echo Router::url('/', true) . 'quiz/view/' . $data['Quiz']['random_id']; ?>"><?php echo $data['Quiz']['name']; ?></a>

<?php echo Router::url('/', true) . 'quiz/view/' . $data['Quiz']['random_id']; ?>