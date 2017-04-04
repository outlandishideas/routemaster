<?php

interface RoutemasterViewInterface
{
    /**
     * @param string $post An ooPost, a post object or a postId that will be used to populate the view's main post property
     * @return string Path to the layout file to wrap the view in
     */
    public function setPost($post);


    /**
     * Renders the content to be displayed
     *
     * @param array $variables   a key/valud array that will be made available to the template
     * @return void
     */
    public function render($variables = array());

}