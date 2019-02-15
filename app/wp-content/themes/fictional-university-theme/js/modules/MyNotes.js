import $ from 'jquery'; // imported by WEbpack

class MyNotes {
    constructor() {
        this.events();
    }

    events () {
        $("#my-notes").on("click", ".delete-note" ,this.deleteNote); // whenever you click in the my-notes element, look for a class element .delete-note and bind the event 
        $("#my-notes").on("click", ".edit-note" ,this.editNote.bind(this)); 
        $("#my-notes").on("click", ".update-note" ,this.updateNote.bind(this)); 
        $(".submit-note").on("click", this.createNote.bind(this));
    }

    // Methods
    createNote(e) {
        
        var ourNewPost = {
            'title': $(".new-note-title").val(),
            'content': $(".new-note-body").val(),
            'status': 'publish' // we cannot rely on client-side data..so we can't set this to private 
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/',
            type: 'POST',
            data: ourNewPost,
            success: (response) => {
                $(".new-note-title, .new-note-body").val('');
                $(`
                
                
            <li data-id="${(response.id)}">
            <input readonly class="note-title-field" type="text" value="${response.title.raw}">
            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
            <textarea readonly class="note-body-field" id="" cols="30" rows="10">${response.content.raw}</textarea>
            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
            </li>
                
                `).prependTo("#my-notes").hide().slideDown();

                console.log('Congrats');
                console.log(response);
            },
            error: (response) => {

                if (~response.responseText.indexOf("ERR_POST_LIMIT")) {
                    $(".note-limit-message").addClass("active");
                }

                console.log('Sorry! Something wrong happened.');
                console.log(response);
          
            }
        });
    }
       

    updateNote(e) {
        var thisNote = $(e.target).parents("li");
        
        var ourUpdatedPost = {
            'title': thisNote.find(".note-title-field").val(),
            'content': thisNote.find(".note-body-field").val()
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'POST',
            data: ourUpdatedPost,
            success: (response) => {
                this.makeNoteReadOnly(thisNote);
                console.log('Congrats');
                console.log(response);
            },
            error: (response) => {
                console.log('Sorry!');
                console.log(response);
          
            }
        });
    }

    deleteNote(e) {
        var thisNote = $(e.target).parents("li");
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'DELETE',
            success: (response) => {
                thisNote.slideUp();
                $(".note-limit-message").removeClass("active");
                console.log('Congrats');
                console.log(response);
            },
            error: (response) => {
                console.log('Sorry!');
                console.log(response);
          
            }
        });
    }

    editNote(e) {
        var thisNote = $(e.target).parents("li");

        if (thisNote.data("state") == "editable") {
            this.makeNoteReadOnly(thisNote);
        }

        else {
            this.makeNoteEditable(thisNote);
        }

    }

    makeNoteEditable(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
        thisNote.find(".note-title-field, .note-body-field").removeAttr('readonly').addClass("note-active-field");
        thisNote.find(".update-note").addClass("update-note--visible");
        thisNote.data("state", "editable");
 
    }

    makeNoteReadOnly(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
        thisNote.find(".note-title-field, .note-body-field").attr('readonly', 'redonly').removeClass("note-active-field");
        thisNote.find(".update-note").removeClass("update-note--visible");
        thisNote.data("state", "not-editable");
    }
}

export default MyNotes;