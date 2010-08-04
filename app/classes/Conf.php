<?php
	class Conf {
		public static function find($namespace = null) {
			return Configure::find($namespace);
		}
		
		public static function exists($namespace) {
			return self::find()->exists($namespace);
		}
		
		public static function delete($namespace = null) {
			return self::find()->delete($namespace);
		}
		
		public static function read($namespace = null, $defaultVar = "\0TripNullString\0\0") {
			return self::find()->read($namespace, $defaultVar);
		}
		
		public static function write($namespace, $value) {
			return self::find()->write($namespace, $value);
		}
		
		private static function getFullNamespace($namespace) {
			return self::find()->getFullNamespace($namespace);
		}
	}
