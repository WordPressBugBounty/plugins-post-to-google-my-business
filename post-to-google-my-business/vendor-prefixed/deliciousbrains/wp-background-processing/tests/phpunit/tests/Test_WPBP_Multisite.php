<?php



require_once __DIR__ . '/../../Test_WP_Background_Process.php';
class Test_WPBP_Multisite extends Test_WP_Background_Process
{
    public function test_get_batch_for_site(){
        $this->assertEmpty( $this->executeWPBPMethod( 'get_batch' ), 'no batches until save' );

        $blog_id = wpmu_create_blog('example.org', '/subsite/', 'Test Subsite', 1);
        $this->assertIsInt($blog_id);

        switch_to_blog($blog_id);
        $this->assertEquals($blog_id, get_current_blog_id());

        $this->wpbp->push_to_queue( 'wibble' );

        $this->wpbp->save();

        $batch_for_blog = $this->executeWPBPMethod( 'get_batch', $blog_id );

        $this->assertNotEmpty( $batch_for_blog );
        $this->assertInstanceOf( 'stdClass', $batch_for_blog );
        $this->assertEquals( array( 'wibble' ), $batch_for_blog->data );

        $batch_for_nonexistent_blog = $this->executeWPBPMethod( 'get_batch', 123 );
        $this->assertEmpty( $batch_for_nonexistent_blog );

        restore_current_blog();

        //Test if we also get the batch outside the site that created it
        $batch_for_blog = $this->executeWPBPMethod( 'get_batch', $blog_id );

        $this->assertNotEmpty( $batch_for_blog );
        $this->assertInstanceOf( 'stdClass', $batch_for_blog );
        $this->assertEquals( array( 'wibble' ), $batch_for_blog->data );

        $this->wpbp->delete($batch_for_blog->key);
        $batch_for_blog = $this->executeWPBPMethod( 'get_batch', $blog_id );
        $this->assertEmpty( $batch_for_blog );
    }

    public function test_get_batches_for_site(){

    }
}