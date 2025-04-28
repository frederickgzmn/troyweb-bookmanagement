<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
    
    // Token on every request to the api server.
    axios.interceptors.request.use((config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    });

    const search_title = ref('');
    const search_results = ref([]);
    const search_done = ref(null);
    const book_list = ref([]);
    const isLoading = ref(false);
    const errorMessages = ref(null);
    const logged = ref(false);
    const sortColumn = ref('');
    const sortOrder = ref('asc');

    // lIst of books from the api server
    const props = defineProps({
        books: Array,
        auth: Object,
    });

    const sortTable = (column) => {
        if (sortColumn.value === column) {
            // Toggle sort order if the same column is clicked
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
        } else {
            // Set the new column and default to ascending order
            sortColumn.value = column;
            sortOrder.value = 'asc';
        }

        // Sort the book_list array
        book_list.value.sort((a, b) => {
            const valueA = a[column] ?? ''; // Handle null or undefined values
            const valueB = b[column] ?? '';

            // Check if the values are numbers
            if (!isNaN(valueA) && !isNaN(valueB)) {
                return sortOrder.value === 'asc' ? valueA - valueB : valueB - valueA;
            }

            // Otherwise, compare as strings
            const stringA = valueA.toString().toLowerCase();
            const stringB = valueB.toString().toLowerCase();

            if (stringA < stringB) return sortOrder.value === 'asc' ? -1 : 1;
            if (stringA > stringB) return sortOrder.value === 'asc' ? 1 : -1;
            return 0;
        });
    };

    // Initializing result load
    book_list.value = props.books;

    // registration request to the api server
    const search_books = async () => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Validate search field
            if ( ! search_title.value ) {
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_search',
                {
                    name: search_title.value
                },
                {
                    headers: { Authorization: "Bearer " + localStorage.getItem('access_token'),
                }
            });

            // Mapping the result variables.
            search_results.value = response.data.result;
            search_done.value = true;

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            if ( search_results.value.length > 0 ) {
                book_list.value = search_results.value;
            } else {
                // All books
                book_list.value = props.books;
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        } finally {
            isLoading.value = false;
        }
    };

    const delete_book = async ( book_id ) => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_delete',
                {
                    book_id: book_id,
                });

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            // If a name is assigned the registration was successful.
            if ( response.data.success ) {
                console.log('Book deleted:', response.data.success);
                router.visit('/books');
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        } finally {
            isLoading.value = false;
        }
    };

    const make_checkout_book = async ( book_id ) => {
        try {
            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_checkout', {
                book_id: book_id,
            });

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        }
    };
</script>

<template>  
    <div>
        <h1>Search Books</h1>
        <form @submit.prevent="search_books">
            <div>
                <label for="search_title">Search Title:</label>
                <input type="text" id="search_title" v-model="search_title" required />
            </div>
            <button type="submit">Search</button>
        </form>

        <div v-if="search_results.length > 0 && search_results.length != 0">
            <span>Search Results ( {{ search_results.length }} ) </span>
        </div>

        <div v-if="search_results.length == 0 && search_done">
            <span> Not found </span>
        </div>

        <div v-if="book_list.length > 0">
            <h2>Book List</h2>
            <table>
                <thead>
                    <tr>
                        <th>
                            <button @click="sortTable('title')" style="background: none; border: none; cursor: pointer;">Title</button>
                        </th>
                        <th>
                            Publisher
                        </th>
                        <th>
                            <button @click="sortTable('author_name')" style="background: none; border: none; cursor: pointer;"> Author </button>
                        </th>
                        <th>
                            <button @click="sortTable('status')" style="background: none; border: none; cursor: pointer;"> Status </button>
                        </th>
                        <th>
                            Category
                        </th>
                        <th>Cover image</th>
                        <th>
                            Average Rating
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="book in book_list" :key="book.id">
                        <td>{{ book.title }}</td>
                        <td>{{ book.publisher_name }}</td>
                        <td>{{ book.author_name }}</td>
                        <td>{{ book.status }}</td>
                        <td>{{ book.category_name }}</td>
                        <td> 
                            <img :src="'/storage/' + book.cover_image" v-if="book.cover_image" alt="Cover Image" style="width: 50px; height: 50px;" />
                        </td>
                        <td>{{ book.average_rating }}</td>
                        <td>
                            <Link :href="'/books/' + book.id + '/show'">View</Link>
                            <!-- Edit a book is the ID 2 -->
                            <Link v-if="auth.user.permissions.original.includes(2)" :href="'/books/' + book.id + '/edit'">Edit</Link>
                            <button @click="delete_book(book.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="book_list.length > 0">
            <h2>Books available</h2>
            <table>
                <thead>
                    <tr>
                        <th>
                            <button @click="sortTable('title')" style="background: none; border: none; cursor: pointer;">Title</button>
                        </th>
                        <th>
                            Publisher
                        </th>
                        <th>
                            <button @click="sortTable('author_name')" style="background: none; border: none; cursor: pointer;"> Author </button>
                        </th>
                        <th>
                            <button @click="sortTable('status')" style="background: none; border: none; cursor: pointer;"> Status </button>
                        </th>
                        <th>
                            Category
                        </th>
                        <th>Cover image</th>
                        <th>
                            Average Rating
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="book in book_list" :key="book.id">
                        <td>{{ book.title }}</td>
                        <td>{{ book.publisher_name }}</td>
                        <td>{{ book.author_name }}</td>
                        <td>{{ book.status }}</td>
                        <td>{{ book.category_name }}</td>
                        <td> 
                            <img :src="'/storage/' + book.cover_image" v-if="book.cover_image" alt="Cover Image" style="width: 50px; height: 50px;" />
                        </td>
                        <td>{{ book.average_rating }}</td>
                        <td>
                            <button @click="make_checkout_book( book.id )" v-if="auth.user.permissions.original.includes(4)"> Mark Checked out </button>
                            <p v-if="!auth.user.permissions.original.includes(4)">Unavailable</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- add a new book is the ID 1 -->
        <Link :href="`/books/create`" v-if="auth.user.permissions.original.includes(1)">Add a new book</Link>
    </div>
</template>