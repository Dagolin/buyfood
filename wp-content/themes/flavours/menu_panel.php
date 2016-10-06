<div id="nav-panel" class="">
    <?php
    // show top navigation and mobile menu
         $TmFlavours = new TmFlavours();
     
        echo tmFlavours_mobile_search();
        echo '<div class="menu-wrap">';
      
        echo tmFlavours_mobile_menu().'</div>';

   
        echo '<div class="menu-wrap">'.tmFlavours_mobile_top_navigation().'</div>';
    ?>
</div>