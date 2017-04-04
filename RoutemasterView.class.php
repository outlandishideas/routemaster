<?php
class RoutemasterView implements RoutemasterViewInterface {

	/** @var $content string The content to be displayed in the layout */
    private $content;

    /** @var $post int|object|ooPost The data to be used in the layout */
    public $post;
    public $logDebug = true;
    public $layoutFile;
    public $theme;

	public function __construct($post = '', $layoutFile = '')
    {
        $this->layoutFile = $layoutFile;
        if($post){
            $this->setPost($post);
        }
    }

    public function setPost($post){
        //set the post object based on the input
        if($post instanceof ooPost){
            $this->post = $post;
        }elseif (is_integer($post) || is_object($post)){
            $this->post = ooRoutemasterPost::getPostObject($post);
        }else{
            global $post;
            $this->post = $post;
        }
    }

    /**
     * @return array[string] An array of validation errors (properties that are not set on this object and which are required by the html template
     */
    public function validation_errors(){
	    $errors = [];
	    if(!method_exists($this->post, 'title')){
            $errors[] =('$this->post->title() is not a method');
        }
	    if(!method_exists($this->post, 'content')){
            $errors[] = ('$this->post->content() is not a method');
        }
        return $errors;
    }

    /**
     * @param bool $force Force rendering of the template even if there are errors
     * @return string $the HMTL of the rendered template
     */
    final public function getHtml($force = false){
        $errors = $this->validation_errors();
	    if($errors && $force == false){
            $return =  "<h2>Error validating template</h2>
                        <ul>";
            foreach($errors as $error){
                $return .= "<li>$error</li>";
            }
            $return .= "</ul>";
            if(WP_DEBUG) $return .= "\n\n".print_r($this, true);
            return $return;
        }else{
            $viewName = get_class($this);

            //buffer the output so that you can return it rather than printing it
            ob_start();

            if (WP_DEBUG && $this->logDebug) echo "\n\n<!-- start $viewName -->\n\n";

            $this->printHtml();
            if (WP_DEBUG && $this->logDebug) echo "\n\n<!-- end $viewName -->\n\n";
            $this->content = ob_get_clean();
            return $this->content;
        }





    }


     protected function printHtml(){
        ?>
<div class="routemaster-view">
    <h2><?php print $this->post->title(); ?></h2>
    <?php print $this->post->content(); ?>
</div>

<?php
     }

    public function render($variables = array()) {
        extract($variables);
        $this->content = $this->getHtml();

		if ($this->layoutFile) {
			include($this->layoutFile);
		} else {
			echo $this->content;
		}
	}
}