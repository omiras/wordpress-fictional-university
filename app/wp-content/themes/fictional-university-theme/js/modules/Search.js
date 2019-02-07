import $ from 'jquery';

class Search {
    
    // 1. Describe and create/initiate our object
    constructor() {
        this.resultsDiv = $("#search-overlay__results");
        this.openButton = $(".js-search-trigger"); 
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term");
        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;
        this.typingTimer;
        this.previousValue;

        this.events();
    }

    // 2. events
    events() {
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));

        // We want to target all the page
        $(document).on("keydown", this.keyPressDispatcher.bind(this));
    
        // This time we only want to bind event to search box
        this.searchField.on("keyup", this.typingLogic.bind(this));
    
    }

    // 3. methods (functions, action...)

    typingLogic() {

        if (this.searchField.val() == this.previousValue) {
            return;
        }

        if (!this.searchField.val()) {
            clearTimeout(this.typingTimer);
            this.isSpinnerVisible = false;
            this.resultsDiv.html('');

            return;
        }
 
        clearTimeout(this.typingTimer);

        if (!this.isSpinnerVisible) {
            this.resultsDiv.html('<div class="spinner-loader"></div>');
            this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 2000)
        this.previousValue = this.searchField.val();
    }

    getResults() {
        this.resultsDiv.html("Imaging real search results here.");
        this.isSpinnerVisible = false;

    }

    keyPressDispatcher(e) {
        
        // second logical operator is needed in order not to call again and again
        // the function in case 'S' key is keep pressed or pressed repeatedly
        if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(":focus")) {
            this.openOverlay();
        }

        else if (e.keyCode == 27 && this.isOverlayOpen) {
            this.closeOverlay();
        }
    }

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.isOverlayOpen = true;
    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false;
    }
}

export default Search;