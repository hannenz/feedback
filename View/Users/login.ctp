<?php
echo $this->Form->create('User', array('action' => 'login'));
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->submit();
echo $this->Form->end();
?>