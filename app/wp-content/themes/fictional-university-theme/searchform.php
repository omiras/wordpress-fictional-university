<form class="search-form" method="get" action="<?php echo esc_url(site_url('/')); ?>"> <!-- Generates home page URL-->
            <label class="headline headline--medium" for="s">Perform a New Search</label>
            <div class="search-form-row">
                <input placeholder="What are you looking for?" class="s" type="search" name="s" id="s">
                <input class="search-submit" type="submit" value="Search">
            </div>
        </form>