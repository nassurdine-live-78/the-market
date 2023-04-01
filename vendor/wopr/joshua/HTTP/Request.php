<?php
    declare(strict_types=1);

    namespace Joshua\HTTP;

    class Request
    {

        private ?string $activity       = NULL;
        private ?array  $post           = NULL;
        private ?array  $get            = NULL;
        private ?array  $file           = NULL;
        private ?string $requestMethod  = NULL;
        private ?string $referer        = NULL;

        public function __construct()
        {
            $this->post             = $_POST;
            $this->get              = $_GET;
            $this->file             = $_FILES;
            $this->requestMethod    = $_SERVER["REQUEST_METHOD"];
            $this->requestURI       = $_SERVER["REQUEST_URI"];
            $this->referer          = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : NULL;
        }

        public function setActivity(?string $activity): void
        {
            $this->activity = $activity;
        }

        public function getActivity(): ?string
        {
            return $this->activity;
        }

        public function post(string $key)
        {
            return (isset($this->post) && isset($this->post[$key])) ? $this->post[$key] : NULL;
        }

        public function postArray()
        {
            return (isset($this->post)) ? $this->post : NULL;
        }

        public function get(string $key)
        {
            return (isset($this->get) && isset($this->get[$key])) ? $this->get[$key] : NULL;
        }

        public function getArray()
        {
            return (isset($this->get)) ? $this->get : NULL;
        }

        public function file(string $key)
        {
            return (isset($this->file) && isset($this->file[$key])) ? $this->file[$key] : NULL;
        }

        public function fileArray()
        {
            return (isset($this->file)) ? $this->file : NULL;
        }

        public function method(): string
        {
            return $this->requestMethod;
        }

        public function URI(): string
        {
            return $this->requestURI;
        }

        public function referer(): string
        {
            return $this->referer;
        }
    }