<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
set_error_handler(
    function ($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, $errno, $errno, $errfile, $errline);
    }
);
assert_options(ASSERT_ACTIVE, TRUE);
assert_options(
    ASSERT_CALLBACK,
    function ($file, $line, $assertion, $message) {
        throw new \Exception("Assertion $assertion failed: $message");
    }
);

define('PACKAGES_FILE_PATH', __DIR__.'/workdir/satis_output/packages.json');
define('PACKAGES_HTML_PATH', __DIR__.'/workdir/satis_output/index.html');

function parse_package_repo()
{
    static $repo;
    if ($repo !== NULL) {
        return $repo;
    }
    $root = json_decode(file_get_contents(PACKAGES_FILE_PATH), TRUE);
    foreach (array_keys($root['includes']) as $file) {
        $path = dirname(PACKAGES_FILE_PATH).'/'.$file;
        assert(file_exists($path));
        $include = json_decode(file_get_contents($path), TRUE);
        foreach ($include['packages'] as $name => $meta) {
            assert(! isset($repo['packages'][$name]), $name.' is unique');
            $repo['packages'][$name] = $meta;
        }
    }

    return $repo;
}

echo "Simple tests\n";
$tests = [
    'packages.json exists at '.PACKAGES_FILE_PATH                    => function () {
        assert(file_exists(PACKAGES_FILE_PATH));
    },
    'Web view exists at '.PACKAGES_HTML_PATH                         => function () {
        assert(file_exists(PACKAGES_HTML_PATH));
    },
    'Can parse packages.json and includes'                           => function () {
        parse_package_repo();
    },
    'Has 1.0.0 of ingenerator/test-satisfy'                          => function () {
        $repo = parse_package_repo();
        assert(
            'https://api.github.com/repos/ingenerator/_satis-build-test-satisfy/zipball/v1.0.0' === $repo['packages']['ingenerator/test-satisfy']['1.0.0']['dist']['url']
        );
    },
    'Has dev-main of ingenerator/test-satisfy'                       => function () {
        $repo = parse_package_repo();
        assert(
            'https://api.github.com/repos/ingenerator/_satis-build-test-satisfy/zipball/7a36d2e7050ab86e7706cf3ef4384a1b4e80a165' === $repo['packages']['ingenerator/test-satisfy']['dev-main']['dist']['url']
        );
    },
    'Can add `require` values from satify-packagelist'               => function () {
        $repo = parse_package_repo();
        assert(
            ['psr/log' => '^1'] === $repo['packages']['ingenerator/test-satisfy']['dev-main']['require']
        );
    },
    'Has 1.5.0 of ingenerator/test-satis (from explicit VCS list)'   => function () {
        $repo = parse_package_repo();
        assert(
            'https://api.github.com/repos/ingenerator/_satis-build-test/zipball/557f78589459ca62f6a856e1a27cb446c06f496e' === $repo['packages']['ingenerator/test-satis']['v1.5.0']['dist']['url']
        );
    },
    'Has 2.x-dev of ingenerator/test-satis (from explicit VCS list)' => function () {
        $repo = parse_package_repo();
        assert(
            'https://api.github.com/repos/ingenerator/_satis-build-test/zipball/ffe7ebd1bae807f146b85e04b0d4bd9fc706c2fe' === $repo['packages']['ingenerator/test-satis']['2.x-dev']['dist']['url']
        );
    },
];

$all_passed = TRUE;
foreach ($tests as $test_name => $test_func) {
    try {
        ob_start();
        $test_func();
        $passed = TRUE;
        $result = 'OK';
    } catch (Throwable $e) {
        $passed = FALSE;
        $result = sprintf(
            '%s [%s:%s]: %s',
            get_class($e),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage()
        );
    }
    $test_output = ob_get_clean();
    $all_passed  = ($all_passed and $passed);
    print(sprintf("\n[%s] %s\n", $passed ? '✓' : '✘', $test_name));
    if ($test_output) {
        $lines = explode("\n", $test_output);
        $lines = array_map(function ($s) { return '    > '.$s; }, $lines);
        print "    Output:\n";
        print(implode("\n", $lines));
        print "\n\n";
    }
    print "    - ".$result."\n";
}

if ($all_passed) {
    echo "\nAll passed!\n";
} else {
    echo "\nFailures\n";
    exit(1);
}
