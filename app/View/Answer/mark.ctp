<?php 
$this->Html->script(
    array(
        'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
        'answer'
    ),
    array(
        'inline' => false
    )
);
$this->assign('title', __('Edit Quiz'));
?>

<form class="form">
    <div class="row">
        <div class="col-md-3 col-xs-3">
            <select name="time_filter" class="form-control">
                <option value="all">All</option>
                <option value="this_year">This Year</option>
                <option value="this_month">This Month</option>
                <option value="this_week">This Week</option>
                <option value="today">Today</option>
            </select>
        </div>
        <div class="col-md-3 col-xs-3">
            <select name="class_filter" class="form-control">
                <option value="all">All</option>
            </select>
        </div>
    </div>
</form>

<table class="table table-condensed table-hover">
    <thead>
        <th>Timestamp</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Class</th>
        <th>Total Points</th>
        
        <?php foreach($data['questions'] as $question): ?>
        <th><?php echo $question['Question']['text'] ?></th>
        <?php endforeach ?>
        
    </thead>
    
    <tbody>
        <?php foreach($data['answers_table'] as $row): ?>
        <tr>
            <td>In Progress</td>
            <td><?php echo $row['Student']['fname'] ?></td>
            <td><?php echo $row['Student']['lname'] ?></td>
            <td><?php echo $row['Student']['class'] ?></td>
            <td>/</td>
            <?php foreach($row['Answer'] as $ans): ?>
            <td><?php echo $ans['answer'] ?></td>
            <?php endforeach ?>
        </tr>
            
        <?php endforeach ?>
        
    </tbody>
</table>