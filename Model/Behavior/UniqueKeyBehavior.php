<?php

class UniqueKeyBehavior extends ModelBehavior {
	
	public function setup(&$model, $settings = array()) {
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = array(
				'callback' => true,
				'field' => 'uid',
				'size' => 3
			);
		}
		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], (array) $settings);
	}
	
	public function beforeSave($model) {
		extract($this->settings[$model->alias]);
		if ($callback) {
			$generate = true;
			if ($model->id) {
				$exist = $model->findById($model->id);
				if (!empty($exist[$model->alias][$field])) {
					$generate = false;
				} else if (!empty($model->data[$model->alias][$field])) {
					$generate = false;
				}
			} else {
				if (!empty($model->data[$model->alias][$field])) {
					$generate = false;
				}
			}
			if ($generate) {
				$model->data[$model->alias][$field] = $this->createUniqueId($model, $field, $size);
			}
		}
		return true;
	}
	
	public function createUniqueId($model, $column = 'uid', $length = 3) {
		$number = '';
		for($i = 0; $i < $length; $i++) {
			$number .= mt_rand(100, 999);
		}
		if(!$model->hasAny(array("{$model->alias}.{$column}" => $number))) {
			return $number;
		} else {
			return $this->createUniqueId($model, $column, $length);
		}
	}
}