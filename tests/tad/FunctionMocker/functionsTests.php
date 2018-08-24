<?php

namespace tad\FunctionMocker;

use PHPUnit\Framework\TestCase;

class functionsTest extends TestCase {
    public function test_throws_when_env_does_not_define_bootstrap_file(){
        $env = _data_dir('no-bootstrap-env');
        
        $this->expectException(UsageError::class);
        
        includeEnvs([$env]);
    }
    
    public function test_allows_for_custom_bootstrap_files(){
        $env = _data_dir('no-bootstrap-env/alternante-bootstrap.php');
        
        includeEnvs([$env]);
    }
}