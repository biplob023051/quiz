<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php echo $this->fetch('meta'); ?>

        <title><?php echo $this->fetch('title'); ?></title>
        <?php echo $this->Html->meta('favicon.ico', '/img/favicon.ico', array('type' => 'icon')); ?>

        <!-- Google Fonts -->
        <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css' />
        <?php
        echo $this->Html->css(array(
            'bootstrap.min',
            'style',
            'print'
        ), array('media' => 'print'));
        echo $this->fetch('css');
        ?>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <style type="text/css">
            @media print {
                .make-grid(sm);

                .visible-xs {
                    .responsive-invisibility();
                }

                .hidden-xs {
                    .responsive-visibility();
                }

                .hidden-xs.hidden-print {
                    .responsive-invisibility();
                }

                .hidden-sm {
                    .responsive-invisibility();
                }

                .visible-sm {
                    .responsive-visibility();
                }
                #result-details {

                }
                #result-details div.text-danger {
                    color: #a94442 !important;
                }
                #result-details div.text-success {
                    color: #3c763d !important;
                }
                #result-details div.text-warning {
                    color: #8a6d3b !important;
                }
                span.score {
                    border-radius: 50% !important;
                    behavior: url(PIE.htc);
                    width: 16px !important;
                    height: 16px !important;
                    padding: 1px 4px !important;
                    background: #fff;
                    border: 2px solid #666;
                    color: #666 !important;
                    text-align: center;
                    font: 14px Arial, sans-serif;
                    font-weight: bold;
                }
                .page-header {
                    border-bottom: 2px solid #eee !important;
                }
            }
        </style>

    </head>
    <body <?php if ($this->request->action == 'login') : ?>style="padding-top:50px;" class="bg-cover"<?php else : ?>style="background:#ffffff; padding-top:50px;"<?php endif; ?>>
        <div class="container">
            <div class="page-header">
                <h3><?php echo __('Quiz name: ') . $quizDetails['Quiz']['name']; ?></h3>
            </div>
            <?php foreach ($quizDetails['Student'] as $key1 => $value1) : ?>
                <div class="row" id="result">
                    <div class="col-md-3 col-xs-4">
                        <?php echo '<b>' . __('Student name: ') . '</b>' . $value1['lname'] . ' ' . $value1['fname']; ?>
                    </div>
                    <div class="col-md-3 col-xs-4">
                        <?php echo '<b>' . __('Quiz taken: ') . '</b>' . $value1['submitted']; ?>
                    </div>
                    <div class="col-md-3 col-xs-2">
                        <?php echo '<b>' . __('Class: ') . '</b>' . $value1['class']; ?>
                    </div>
                    <div class="col-md-3 col-xs-2">
                        <?php 
                            foreach ($quizDetails['Ranking'] as $key2 => $value2) { 
                                if ($value1['id'] == $value2['student_id']) {  
                                echo '<b>' . __('Total: ') . '</b>' . $value2['score'] . '/' . $value2['total'];  break; 
                                }  
                            } 
                        ?>
                    </div>
                    <?php $i = 0; foreach ($quizDetails['Question'] as $key3 => $value3): $i++; ?>
                        <?php 
                            $answer = '';
                            foreach ($value3['Answer'] as $key4 => $value4) { 
                                if ($value1['id'] == $value4['student_id']) {
                                    if (empty($value4['text'])) { 
                                        $answer = 'Not Answered'; 
                                    } else { 
                                        $answer = $answer . ' ' . $value4['text']; 
                                    }  
                                } 
                            }
                        ?>
                        <?php if (strlen($i . ') ' . $value3['text']) < 45 && strlen($answer) < 45) : ?>
                            <div class="col-md-12 col-xs-6">
                                <?php echo $i . ') ' . $value3['text']; ?>
                            </div>
                            <div class="col-md-12 col-xs-6" id="result-details">
                                <?php echo $this->element('Quiz/answer-print', array('value3' => $value3, 'value1' => $value1, 'inline' => true)); ?>
                            </div>
                        <?php else: ?>
                            <div class="col-md-12 col-xs-12">
                                <?php echo $i . ') ' . $value3['text']; ?>
                            </div>
                            <div class="col-md-12 col-xs-12" id="result-details">
                                <?php echo $this->element('Quiz/answer-print', array('value3' => $value3, 'value1' => $value1)); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
        <div id="footer">
            <div class="container"></div>
        </div>

        <?php
        echo $this->Html->script(array(
            'jquery.min.js',
            'bootstrap.min.js'
        ));
        ?>
        <?php echo $this->fetch('script'); ?>
    </body>
</html>