<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';

    const book_title = ref('');
    const book_author = ref('');
    const book_description = ref('');
    const book_publication_date = ref('');
    const book_category = ref('');
    const book_isbn = ref('');
    const book_page_count = ref('');
    const book_cover_image = ref(null);
    const book_publisher = ref('');

    // lIst of books from the api server
    const props = defineProps({
        book: Array,
        customer_reviews: Array,
        auth: Object,
    });

    book_title.value = props.book.title;
    book_author.value = props.book.author_id;
    book_description.value = props.book.description;
    book_publisher.value = props.book.publisher_id;
    book_publication_date.value = props.book.publication_date;
    book_category.value = props.book.category_id;
    book_isbn.value = props.book.isbn;
    book_page_count.value = props.book.page_count;
    book_cover_image.value = '/storage/' + props.book.cover_image;

    // Review variables.
    const review_title = ref('');
    const review_comment = ref('');
    const review_rating = ref('');

    const submit_review = async () => {
        try {
            // Validate input fields
            if (!review_title.value || !review_comment.value || !review_rating.value) {
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/review_create', {
                book_id: props.book.id,
                title: review_title.value,
                comment: review_comment.value,
                rating_stars: review_rating.value,
            });

            if (response.data.success) {
                // Reset the review form fields
                review_title.value = '';
                review_comment.value = '';
                review_rating.value = '';

                // Optionally, you can show a success message or update the reviews list
            }
        } catch (error) {
            console.error('Error submitting review:', error);
        }
    };
</script>

<template>
    <div>
        <h1>Book Details</h1>
        <div>
            <h2>{{ book_title }}</h2>
            <p><strong>Author:</strong> {{ book_author }}</p>
            <p><strong>Description:</strong> {{ book_description }}</p>
            <p><strong>Publication Date:</strong> {{ book_publication_date }}</p>
            <p><strong>Category:</strong> {{ book_category }}</p>
            <p><strong>ISBN:</strong> {{ book_isbn }}</p>
            <p><strong>Page Count:</strong> {{ book_page_count }}</p>
            <img :src="book_cover_image" alt="Book Cover"/>
        </div>
    </div>
    <div>
        <h2>Customer Reviews</h2>
        <ul v-if="customer_reviews.length > 0">
            <li v-for="review in customer_reviews" :key="review.id">
                <p><strong>{{ review.title }}</strong> by {{ review.user_name }}</p>
                <p>{{ review.comment }}</p>
                <p><strong>Rating:</strong> {{ review.rating_stars }}</p>
            </li>
        </ul>
        <p v-else>No reviews available.</p>
    </div>
    <div>
        <h2>Write a Review</h2>
        <form @submit.prevent="submit_review">
            <div>
                <label for="review_title">Title:</label>
                <input type="text" id="review_title" v-model="review_title" required />
            </div>
            <div>
                <label for="review_comment">Comment:</label>
                <textarea id="review_comment" v-model="review_comment" required></textarea>
            </div>
            <div>
                <label for="review_rating">Rating:</label>
                <select id="review_rating" v-model="review_rating" required>
                    <option value="" disabled>Select a rating</option>
                    <option v-for="rating in 5" :key="rating" :value="rating">{{ rating }}</option>
                </select>
            </div>
            <button type="submit">Submit Review</button>
        </form>
    </div>
    <div>
        <Link href="/books">Back to Book List</Link>
        <!-- Edit a book is the ID 2 -->
        <Link v-if="auth.user.permissions.original.includes(2)" :href="'/books/' + book.id + '/edit'">Edit</Link>
    </div>
</template>