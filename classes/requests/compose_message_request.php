<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_quickmail
 * @copyright  2008-2017 Louisiana State University
 * @copyright  2008-2017 Adam Zapletal, Chad Mazilly, Philip Cali, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\requests;

use block_quickmail\forms\compose_message_form;

class compose_message_request extends \block_quickmail_request {

    public $form;

    public $course;
    
    public $data;
    
    protected static $public_attributes = [
        'subject',
        'noreply_email',
        'additional_emails',
        'message',
        'signature_id',
        'receipt'
    ];

    /**
     * Construct the message submission request
     * 
     * @param compose_message_form  $compose_message_form  (extends moodleform)
     */
    public function __construct(compose_message_form $compose_message_form) {
        $this->form = $compose_message_form;
        $this->form_data = ! empty($this->form) ? $this->form->get_data() : null;
        $this->course = $this->get_request_course();
    }

    public function get_request_course() {
        return $this->form->course;
    }

    /////////////////////////////////////////////////////////////
    ///
    ///  STATIC METHODS
    ///
    /////////////////////////////////////////////////////////////

    /**
     * Instantiates and returns a compose message request
     * @param  \compose_message_form   $compose_message_form
     * @return \compose_message_request
     */
    public static function make_compose_request(compose_message_form $compose_message_form) {
        // instantiate "compose message" request
        $request = new self($compose_message_form);

        return $request;
    }

    /////////////////////////////////////////////////////////////
    ///
    ///  FORM SUBMISSION ACTIONS
    ///
    /////////////////////////////////////////////////////////////

    /**
     * Helper function to report whether or not the request was submitted with intent to save
     * 
     * @return bool
     */
    public function to_save_draft() {
        return $this->was_submitted('save');
    }

    /**
     * Helper function to report whether or not the request was submitted with intent to send
     * 
     * @return bool
     */
    public function to_send_message() {
        return $this->was_submitted('send');
    }

    /////////////////////////////////////////////////////////////
    ///
    ///  ATTRIBUTES
    ///
    /////////////////////////////////////////////////////////////

    /**
     * Returns the message subject
     * 
     * @return string
     */
    private function subject($form_data = null) {
        return ! empty($form_data) ? (string) $this->form->get_data()->subject : '';
    }

    /**
     * Returns the no-reply email address which the message will be sent from
     * 
     * @return string
     */
    private function noreply_email($form_data = null) {
        return ! empty($form_data) ? (string) $this->form->get_data()->noreply : '';
    }

    /**
     * Returns an array of emails that will also receive the message
     * 
     * @return array
     */
    private function additional_emails($form_data = null) {
        return ! empty($form_data) ? array_unique(explode(',', $this->form->get_data()->additional_emails)) : [];
    }

    /**
     * Returns the message body
     * 
     * @return string
     */
    private function message($form_data = null) {
        // just FYI, available: text, format, itemid
        return ! empty($form_data) ? (string) $this->form->get_data()->message_editor['text'] : '';
    }

    /**
     * Returns the id of the user's selected signature id
     * 
     * @return int
     */
    private function signature_id($form_data = null) {
        return ! empty($form_data) ? (int) $this->form->get_data()->signature_id : 0;
    }

    /**
     * Returns the user's preference on whether or not to receive a copy of the message themselves
     * 
     * @return bool
     */
    private function receipt($form_data = null) {
        return ! empty($form_data) ? (bool) $this->form->get_data()->receipt : false;
    }

    /////////////////////////////////////////////////////////////
    ///
    ///  REDIRECTS
    ///
    /////////////////////////////////////////////////////////////

    /**
     * Returns a redirect header back to the request's course page
     * 
     * @return (http redirect header)
     */
    public function redirect_back_to_course() {
        $url = new moodle_url('/course/view.php', ['id' => $this->course->id]);

        redirect($url, block_quickmail_plugin::_s('redirect_back_to_course_from_message', $this->course->fullname), 2);
    }

}
