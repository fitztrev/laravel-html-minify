<?php

use Mockery as m;
use Fitztrev\LaravelHtmlMinify\LaravelHtmlMinifyCompiler;

class MinifyTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
		$this->compiler = new LaravelHtmlMinifyCompiler(m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
	}

	/* *** */

	public function testRemoveHtmlComments() {
		$string = '<html>
			<body>
				<!-- start content -->
				<p>hello</p> <!-- greet the user -->
				<!--
					<p>old code that is commented out</p>
				-->
				<!-- end content -->
			</body>
		</html>';
		$expected = '<html> <body> <p>hello</p> </body> </html>';

		$result = $this->compiler->compileString($string);
		$this->assertEquals( $expected, $result );
	}

	public function testKeepConditionalComments() {
		$string = '<html>
			<body>
				<!--[if IE 6]>
					<p>hello, IE6 user</p>
				<![endif]-->

				<!--[if IE 8]><p>hello, IE8 user</p><![endif]-->
			</body>
		</html>';
		$expected = '<html> <body> <!--[if IE 6]> <p>hello, IE6 user</p> <![endif]--> <!--[if IE 8]><p>hello, IE8 user</p><![endif]--> </body> </html>';

		$result = $this->compiler->compileString($string);
		$this->assertEquals( $expected, $result );
	}

	/* *** */

	public function testPreTag() {
		$string = '<html>
			<body>
				<pre>hello</pre>
			</body>
		</html>';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	public function testPreTagWithClass() {
		$string = '<html>
			<body>
				<pre class="test">hello</pre>
			</body>
		</html>';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	public function testTextareaTag() {
		$string = '<html>
			<body>
				<textarea>hello</textarea>
			</body>
		</html>';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	public function testTextareaTagWithAttributes() {
		$string = '<html>
			<body>
				<textarea rows="5" cols="5"">hello</textarea>
			</body>
		</html>';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	/* *** */

	public function testSingleExternalScriptTag() {
		$string = '<html>
			<head>
				<script type="text/javascript" src="script.js"></script>
			</head>
		</html>
		';
		$this->assertTrue( $this->compiler->shouldMinify($string) );
	}

	public function testSingleExternalScriptTagWithCacheBuster() {
		$string = '<html>
			<head>
				<script type="text/javascript" src="script.<?php echo filemtime("script.js"); ?>.js"></script>
			</head>
		</html>
		';
		$this->assertTrue( $this->compiler->shouldMinify($string) );
	}

	public function testMultipleExternalScriptTag() {
		$string = '<html>
			<head>
				<script type="text/javascript" src="script1.js"></script>
				<script type="text/javascript" src="script2.js"></script>
			</head>
		</html>
		';
		$this->assertTrue( $this->compiler->shouldMinify($string) );
	}

	public function testExternalAndEmbeddedScriptTag() {
		$string = '<html>
			<head>
				<script type="text/javascript" src="script.js"></script>
				<script type="text/javascript">
					alert("ok");
				</script>
			</head>
		</html>
		';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	public function testGoogleAdSenseEmbedTag() {
		$string = '<html>
			<body>
				<script type="text/javascript"><!--
					google_ad_client = "ca-pub-XXX";
					/* faviconit */
					google_ad_slot = "XXX";
					google_ad_width = 300;
					google_ad_height = 600;
					//-->
				</script>
				<script type="text/javascript"
					src="//pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
			</body>
		</html>';

		$expected = '<html>
			<body>
				<script type="text/javascript"><!--
					google_ad_client = "ca-pub-XXX";
					/* faviconit */
					google_ad_slot = "XXX";
					google_ad_width = 300;
					google_ad_height = 600;
					//-->
				</script>
				<script type="text/javascript"
					src="//pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
			</body>
		</html>';

		$result = $this->compiler->compileString($string);
		$this->assertEquals( $expected, $result );
	}

	public function testEmbeddedScriptTagSingleLine() {
		$string = '<html>
			<head>
				<script>alert("ok");</script>
			</head>
		</html>
		';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	public function testEmbeddedScriptTagMultipleLines() {
		$string = '<html>
			<head>
				<script>
					alert("ok");
					alert("ok");
				</script>
			</head>
		</html>
		';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	/* *** */

	public function testValueWithoutMultipleSpacesSingleWord() {
		$string = '<html>
			<body>
				<form>
					<input type="submit" value="Submit" />
				</form>
			</body>
		</html>';
		$this->assertTrue( $this->compiler->shouldMinify($string) );
	}

	public function testValueWithoutMultipleSpacesSingleWordSingleQuotes() {
		$string = '<html>
			<body>
				<form>
					<input type="submit" value=\'Submit\' />
				</form>
			</body>
		</html>';
		$this->assertTrue( $this->compiler->shouldMinify($string) );
	}

	public function testValueWithoutMultipleSpacesMultipleWords() {
		$string = '<html>
			<body>
				<form>
					<input type="submit" value="Add Document" />
				</form>
			</body>
		</html>';
		$this->assertTrue( $this->compiler->shouldMinify($string) );
	}

	public function testValueWithMultipleSpaces() {
		$string = '<html>
			<body>
				<form>
					<input type="submit" value="     Submit     " />
				</form>
			</body>
		</html>';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	public function testValueWithMultipleSpacesSingleQuotes() {
		$string = '<html>
			<body>
				<form>
					<input type="submit" value=\'     Submit     \' />
				</form>
			</body>
		</html>';
		$this->assertFalse( $this->compiler->shouldMinify($string) );
	}

	/* *** */

	public function testAllowedHtml() {
		$string = '<html>
			<body>
				<p>hello</p>
			</body>
		</html>';
		$expected = '<html> <body> <p>hello</p> </body> </html>';

		$result = $this->compiler->compileString($string);
		$this->assertEquals( $expected, $result );
	}

	public function testMultipleSpaces() {
		$string = '<html>
			<body>
				<p>hello  with     random     spaces</p>
			</body>
		</html>';
		$expected = '<html> <body> <p>hello with random spaces</p> </body> </html>';

		$result = $this->compiler->compileString($string);
		$this->assertEquals( $expected, $result );
	}

	public function testPHPTags() {
		$string = '<?php
echo "hello";
?>';
		$expected = '<?php echo "hello";?>';

		$result = $this->compiler->compileString($string);
		$this->assertEquals( $expected, $result );
	}

}
