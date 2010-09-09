<?php

class Mapping
{
    /**
     * Mapping of caches table
     *
     * @var array
     */
    public static $caches = array (
    'Language' => 'language_id',
    'LanguageName' => 'language_name',
    'Course' => 'course',
    'Teacher' => 'teacher',
    'Newbie' => 'level_newbie',
    'Basic' => 'level_basic',
    'Intermediate' => 'level_intermediate',
    'Proficient' => 'level_proficient',
    'Native' => 'native',
    'NativeTotal' => 'native_total',
    'LevelTotal' => 'level_total');

    /**
     * Mapping of courses table
     *
     * @var array
     */
    public static $courses = array (
    'Id' => 'id',
    'Teacher' => 'user_id',
    'Original' => 'original_id',
    'Group' => 'group_id',
    'Name' => 'title',
    'Intro' => 'description',
    'Language' => 'language_id',
    'Level' => 'level',
    'Status' => 'status',
    'Share' => 'share',
    'Created' => 'created',
    'Updated' => 'modified');

    /**
     * Mapping of classes table
     *
     * @var array
     */
    public static $classes = array (
    'Id' => 'id',
    'Teacher' => 'user_id',
    'Time' => 'time',
    'Opened' => 'opened',
    'Finished' => 'finished',
    'Created' => 'created',
    'Updated' => 'modified');

    /**
     * Mapping of classes_lessons table
     *
     * @var array
     */
    public static $classes_lessons = array (
    'Class' => 'class_id',
    'Lesson' => 'lesson_id',
    'Created' => 'created',
    'Updated' => 'modified');

    /**
     * Mapping of classes_users table
     *
     * @var array
     */
    public static $classes_users = array (
    'Class' => 'class_id',
    'Student' => 'student_id',
    'Joined' => 'joined',
    'Confirmed' => 'confirmed',
    'Created' => 'created',
    'Updated' => 'modified');

    /**
     * Mapping of classes_emails table
     *
     * @var array
     */
    public static $classes_emails = array (
    'Class' => 'class_id',
    'Message' => 'content',
    'Created' => 'created');

    /**
     * Mapping of countries table
     *
     * @var array
     */
    private static $countries = array (
    'Id' => 'id',
    'Name' => 'name');

    /**
     * Mapping of course table
     *
     * @var array
     */
    public static $course = array (
    'Id' => 'id',
    'Name' => 'title',
    'Intro' => 'description',
    'Teacher' => 'user_id',
    'Original' => 'original_id',
    'Level' => 'level',
    'Share' => 'share',
    'Status' => 'status',
    'Group' => 'group_id',
    'Language' => 'language_id',
    'Created' => 'created',
    'Updated' => 'modified');

    public function course()
    {
        return self::$course;
    }

    public static function __call($mapping, $args)
    {
        return self::$$mapping;
    }
}

$a = new Mapping();
Mapping::$courses;
Mapping::$courses;
$x = Mapping::$courses;
Mapping::$courses;
Mapping::$courses;

?>