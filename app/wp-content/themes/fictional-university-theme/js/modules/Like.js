import $ from 'jquery';

class Like {
    constructor() {
        this.events();
    }

    events() {
        $(".like-box").on("click", this.ourClickDispatcher.bind(this));
    }

    ourClickDispatcher(e) {

        // We want people let be able to click anywhere near the 'harth' icon
        // This way we make Javascript more flexible
        var currentLikeBox = $(e.target).closest(".like-box"); 

        if (currentLikeBox.attr('data-exists') == "yes") {
            this.deleteLike(currentLikeBox);
        }

        else {
            this.createLike(currentLikeBox);
        }
    }

    createLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            type: 'POST',
            data: {'professorId': currentLikeBox.data('professor')}, // It is the same way that manageLike?professorId=789
            success: (response) => {
                currentLikeBox.attr('data-exists', 'yes');

                var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
                likeCount++;
                currentLikeBox.find(".like-count").html(likeCount);

                currentLikeBox.attr('data-like', response); // WP send back the ID Like number of the post

                console.log('Congrats');
                console.log(response);
            },
            error: (response) => {
                console.log('Sorry!');
                console.log(response);
          
            }
        });    
    }

    deleteLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            data: {'like': currentLikeBox.attr('data-like')},
            type: 'DELETE',
            success: (response) => {
                currentLikeBox.attr('data-exists', 'no');

                var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
                likeCount--;
                currentLikeBox.find(".like-count").html(likeCount);

                currentLikeBox.attr('data-like', ''); 

                console.log('Congrats');
                console.log(response);
            },
            error: (response) => {
                console.log('Sorry!');
                console.log(response);
          
            }
        }); 
    }

}

export default Like;