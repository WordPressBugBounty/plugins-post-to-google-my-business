<?php

class Test_Is_Multisite extends WP_UnitTestCase
{

    /**
     * Basic test to see if everything works, and we're working in multisite
     */
    public function test_is_multisite(){
        $this->assertTrue(is_multisite());
    }
}