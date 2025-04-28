<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';
    import axios from 'axios';

    const title = ref('');
    const author = ref('');
    const description = ref('');
    const publication_date = ref('');
    const category = ref('');
    const isbn = ref('');
    const page_count = ref('');
    const cover_image = ref(null);
    const publisher = ref('');
    const isLoading = ref(false);
    const errorMessages = ref([]);
    const notificationSuccess = ref(null);

    // lIst of books from the api server
    const props = defineProps({
        book: Array,
        miscellaneous: Array,
        permission: Array,
    });

    // Mapping selected book data to the input fields.
    title.value = props.book.title;
    author.value = props.book.author_id;
    description.value = props.book.description;
    publisher.value = props.book.publisher_id;
    publication_date.value = props.book.publication_date;
    category.value = props.book.category_id;
    isbn.value = props.book.isbn;
    page_count.value = props.book.page_count;
    

    // Handle file input change
    const onFileChange = (event) => {
        // console.log('File:', event.target.files[0]);
        const file = event.target.files[0];
        cover_image.value = file; // Store the selected file in the cover_image variable
    };

    const edit_book = async () => {
        isLoading.value = true;

        try {
            // Validate search field
            if ( ! title.value || ! author.value || ! description.value || ! publication_date.value || ! category.value || ! isbn.value || ! page_count.value ) {
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_edit',
                {
                    book_id: props.book.id,
                    title: title.value,
                    author: author.value,
                    description: description.value,
                    cover_image: cover_image.value,
                    publisher: publisher.value,
                    publication_date: publication_date.value,
                    category: category.value,
                    isbn: isbn.value,
                    page_count: page_count.value,
                }, {
                    headers: { Authorization: "Bearer " + localStorage.getItem('access_token'), 'Content-Type': 'multipart/form-data', },
                });

                if ( response.data.success ) {
                    notificationSuccess.value = response.data.success;

                    // Redirect to login page after 2 secnds.
                    setTimeout(() => {
                        router.visit('/books');
                    }, 2000 );

                }

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        } finally {
            isLoading.value = false;
        }
    };
</script>

<template>
    <div>
        <h1>Edit a New Book</h1> <strong>{{ book.id }}</strong>
        <form @submit.prevent="edit_book" v-if="permission">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" v-model="title" required />
            </div>

            <div>
                <label for="author">Author:</label>
                <select id="author" v-model="author" required>
                    <option value="" disabled>Select an author</option>
                    <option v-for="author in miscellaneous.author" :key="author.id" :value="author.id">{{ author.name }}</option>
                </select>
            </div>

            <div>
                <label for="description">Description:</label>
                <textarea id="description" v-model="description" required></textarea>
            </div>

            <div>
                <label for="cover_image">Cover Image:</label>
                <input type="file" id="cover_image" @change="onFileChange" />
            </div>

            <div>
                <label for="publisher">Publisher:</label>
                <select id="publisher" v-model="publisher" required>
                    <option value="" disabled>Select an publisher</option>
                    <option v-for="publisher in miscellaneous.publisher" :key="publisher.id" :value="publisher.id">{{ publisher.name }}</option>
                </select>
            </div>

            <div>
                <label for="publication_date">Publication Date:</label>
                <input type="date" id="publication_date" v-model="publication_date" required />
            </div>

            <div>
                <label for="category">category:</label>
                <select id="category" v-model="category" required>
                    <option value="" disabled>Select an category</option>
                    <option v-for="category in miscellaneous.category" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
            </div>

            <div>
                <label for="isbn">ISBN:</label>
                <input type="text" id="isbn" v-model="isbn" required />
            </div>

            <div>
                <label for="page_count">Page Count:</label>
                <input type="number" id="page_count" v-model="page_count" required />
            </div>

            <button type="submit">Edit Book</button>
        </form>

        <!-- Display validation errors -->
        <div v-if="errorMessages.length" class="error-messages">
            <ul>
                <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
            </ul>
        </div>

        <!-- Display success message -->
        <div v-if="notificationSuccess" class="success-message">
            <p>{{ notificationSuccess }}</p>
        </div>
    </div>
</template>

<style>
    .error-messages {
        color: red;
    }
</style>