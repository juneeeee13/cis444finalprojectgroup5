document.addEventListener("DOMContentLoaded", function () {
    var postButton = document.getElementById('postButton');
    var postModal = document.getElementById('postModal');
    var closeButton = document.querySelector('.close');
    var postForm = document.getElementById('postForm');
    var postsSection = document.getElementById('postsSection');

    var replyModal = document.getElementById('replyModal');
    var replyForm = document.getElementById('replyForm');
    var activeReplyTarget;

    // "Post"ボタンを押すとモーダルを表示
    // Display the modal when the "Post" button is clicked
    postButton.addEventListener('click', function () {
        postModal.style.display = 'block';
    });

    // モーダルの閉じるボタンを押すとモーダルを非表示に
    // Hide the modal when the close button is clicked
    document.querySelectorAll('.close').forEach(function (closeBtn) {
        closeBtn.addEventListener('click', function () {
            postModal.style.display = 'none';
            replyModal.style.display = 'none';
        });
    });

    // モーダル外をクリックするとモーダルを閉じる
    // Close the modal when clicking outside the modal
    window.addEventListener('click', function (event) {
        if (event.target == postModal) {
            postModal.style.display = 'none';
        } else if (event.target == replyModal) {
            replyModal.style.display = 'none';
        }
    });

    // 日付と時刻をフォーマットする関数
    // Function to format date and time
    function formatDate(date) {
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        var hours = ('0' + date.getHours()).slice(-2);
        var minutes = ('0' + date.getMinutes()).slice(-2);
        return `${year}-${month}-${day} ${hours}:${minutes}`;
    }

    // フォームの送信処理（投稿）
    // Form submission process (for posting)
    postForm.addEventListener('submit', function (event) {
        event.preventDefault();

        // ユーザーが入力したテキストと選択した画像を取得
        // Retrieve the text entered by the user and the selected image
        var postText = document.getElementById('postText').value;
        var postImageFile = document.getElementById('postImage').files[0];

        // 新しい投稿を作成する
        // Create a new post
        var postDiv = document.createElement('div');
        postDiv.classList.add('post');
        postDiv.style.border = '1px solid #ddd';
        postDiv.style.padding = '10px';
        postDiv.style.margin = '10px 0';

        // 現在の日時を取得
        // Get the current date and time
        var currentDate = new Date();
        var formattedDate = formatDate(currentDate);

        // ユーザー情報を追加
        // Add user information
        var userName = document.createElement('span');
        userName.textContent = 'User123';
        var userId = document.createElement('span');
        userId.textContent = ' (@user123)';
        var dateSpan = document.createElement('span');
        dateSpan.textContent = ' - ' + formattedDate;
        postDiv.appendChild(userName);
        postDiv.appendChild(userId);
        postDiv.appendChild(dateSpan);

        // テキスト部分
        // Text part
        var textParagraph = document.createElement('p');
        textParagraph.textContent = postText;
        postDiv.appendChild(textParagraph);

        // 画像部分
        // Image part
        if (postImageFile) {
            var imageElement = document.createElement('img');
            imageElement.src = URL.createObjectURL(postImageFile);
            imageElement.style.maxWidth = '100%';
            postDiv.appendChild(imageElement);
        }

        // 返信ボタンを追加
        // Add a reply button
        var replyButton = document.createElement('button');
        replyButton.textContent = 'Reply';
        replyButton.addEventListener('click', function () {
            activeReplyTarget = postDiv;
            replyModal.style.display = 'block';
        });
        postDiv.appendChild(replyButton);

        // "いいね"ボタンを追加
        // Add a "like" button
        var likeButton = document.createElement('button');
        likeButton.innerHTML = '♥';
        likeButton.classList.add('like-button');

        var likeCount = 0; // Initial value is 0
        var likeCountSpan = document.createElement('span');
        likeCountSpan.classList.add('like-count');
        likeCountSpan.textContent = ` ${likeCount} Likes`;

        likeButton.addEventListener('click', function () {
            likeCount++;
            likeCountSpan.textContent = ` ${likeCount} Likes`;
        });

        postDiv.appendChild(likeButton);
        postDiv.appendChild(likeCountSpan);

        // 削除ボタンを追加
        // Add a delete button
        var deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', function () {
            // ユーザーに確認ダイアログを表示
            // Display a confirmation dialog to the user
            var confirmDelete = confirm("Are you sure you want to delete this post?");
            if (confirmDelete) {
                postDiv.remove(); // If "Yes" is selected, delete the post
            }
        });
        postDiv.appendChild(deleteButton);

        // 返信表示用のセクション
        // Section for displaying replies
        var repliesDiv = document.createElement('div');
        repliesDiv.classList.add('replies');
        repliesDiv.style.marginLeft = '20px';
        postDiv.appendChild(repliesDiv);

        // 新しい投稿を一番上に追加
        // Add the new post at the top
        if (postsSection.firstChild) {
            postsSection.insertBefore(postDiv, postsSection.firstChild);
        } else {
            postsSection.appendChild(postDiv);
        }

        // フォームをリセットしてモーダルを閉じる
        // Reset the form and close the modal
        postForm.reset();
        postModal.style.display = 'none';
    });

    // 返信フォームの送信処理
    // Reply form submission process
    replyForm.addEventListener('submit', function (event) {
        event.preventDefault();

        var replyText = document.getElementById('replyText').value;

        // 新しい返信を作成
        // Create a new reply
        var replyDiv = document.createElement('div');
        replyDiv.classList.add('reply');
        replyDiv.style.border = '1px solid #ddd';
        replyDiv.style.padding = '10px';
        replyDiv.style.margin = '10px 0';

        // 現在の日時を取得
        // Get the current date and time
        var currentDate = new Date();
        var formattedDate = formatDate(currentDate);

        // ユーザー情報を追加
        // Add user information
        var replyUserName = document.createElement('span');
        replyUserName.textContent = 'UserReply';
        var replyUserId = document.createElement('span');
        replyUserId.textContent = ' (@userReply)';
        var dateSpan = document.createElement('span');
        dateSpan.textContent = ' - ' + formattedDate;
        replyDiv.appendChild(replyUserName);
        replyDiv.appendChild(replyUserId);
        replyDiv.appendChild(dateSpan);

        // 返信テキスト部分
        // Reply text part
        var replyParagraph = document.createElement('p');
        replyParagraph.textContent = replyText;
        replyDiv.appendChild(replyParagraph);

        // 返信ボタンを追加してネストされた返信も可能に
        // Add a reply button to allow nested replies
        var nestedReplyButton = document.createElement('button');
        nestedReplyButton.textContent = 'Reply';
        nestedReplyButton.addEventListener('click', function () {
            activeReplyTarget = replyDiv;
            replyModal.style.display = 'block';
        });
        replyDiv.appendChild(nestedReplyButton);

        // 返信表示用のセクション（ネストされた返信用）
        // Section for displaying replies (for nested replies)
        var nestedRepliesDiv = document.createElement('div');
        nestedRepliesDiv.classList.add('replies');
        nestedRepliesDiv.style.marginLeft = '20px';
        replyDiv.appendChild(nestedRepliesDiv);

        var repliesDiv = activeReplyTarget.querySelector('.replies');
        repliesDiv.appendChild(replyDiv);

        // フォームをリセットしてモーダルを閉じる
        // Reset the form and close the modal
        replyForm.reset();
        replyModal.style.display = 'none';
    });
});
