<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
/* Slide-in Animation */
@keyframes slideInUp {
    0% { transform: translateY(40px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

.animate-slide-in {
    animation: slideInUp 0.35s cubic-bezier(0.25, 0.8, 0.5, 1);
}

/* Dark Mode Overrides for Modal Content */
@media (prefers-color-scheme: dark) {
    #modalContent {
        background: rgba(30, 41, 59, 0.95) !important; /* Slate-800 */
        color: #e2e8f0 !important;
        border-color: rgba(255, 255, 255, 0.1);
    }
    #modalContent input,
    #modalContent textarea {
        background: #1f2937 !important; /* Gray-800 */
        color: #fff !important;
        border-color: #4b5563 !important; /* Gray-600 */
    }
    #modalContent .ql-toolbar.ql-snow {
        background: #334155 !important; /* Slate-700 */
        border-color: #4b5563 !important;
    }
    #modalContent .ql-container.ql-snow {
        background: #1f2937 !important;
        border-color: #4b5563 !important;
        color: #fff !important;
    }
    #modalContent .ql-editor {
        color: #e2e8f0;
    }
    #modalTitle {
        color: #6366f1 !important; /* Indigo-500 */
    }
}
</style>

<div id="modalOverlay"
     class="fixed inset-0 z-[100] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

    <div id="modalContent"
         class="w-full max-w-4xl rounded-xl shadow-3xl bg-white backdrop-blur-xl 
                border border-slate-200 flex flex-col max-h-[95vh] overflow-hidden animate-slide-in transition-shadow duration-300">

        <div class="sticky top-0 z-10 bg-white/90 backdrop-blur-md border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h2 id="modalTitle" class="text-2xl font-extrabold text-indigo-600">Add News</h2>

            <button onclick="closeModal()"
                    title="Close"
                    class="p-2 rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-800 active:scale-95 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="overflow-y-auto px-6 py-8 space-y-7">

            <form id="newsForm" method="POST" enctype="multipart/form-data" class="space-y-6">

                <input type="hidden" name="id" id="postId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="font-semibold text-slate-700 mb-1.5 block">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition"
                               required>
                    </div>

                    <div>
                        <label for="subheading" class="font-semibold text-slate-700 mb-1.5 block">Subheading / Summary</label>
                        <input type="text" id="subheading" name="subheading"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="author" class="font-semibold text-slate-700 mb-1.5 block">Author</label>
                        <input type="text" id="author" name="author"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label for="category" class="font-semibold text-slate-700 mb-1.5 block">Category / Tag</label>
                        <input type="text" id="category" name="category"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label for="description" class="font-semibold text-slate-700 mb-1.5 block">Short Description (for listing/SEO)</label>
                    <textarea id="description" name="description"
                              class="w-full border border-slate-300 rounded-lg px-4 py-2.5 shadow-sm h-24 resize-none focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
                </div>

                <div>
                    <label for="quillEditor" class="font-semibold text-slate-700 mb-1.5 block">Full Content</label>

                    <textarea id="content" name="content" class="hidden"></textarea>

                    <div id="quillEditor" class="border border-slate-300 rounded-lg shadow-sm bg-white" style="height: 250px;"></div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <label class="font-semibold text-slate-700 mb-3 block">Featured Image</label>

                    <div class="flex flex-wrap items-end gap-6">
                        
                        <div id="currentImageContainer" class="hidden">
                            <p class="text-xs text-slate-500 mb-1">Current Image:</p>
                            <img id="currentImagePreview" src=""
                                 class="w-40 h-28 object-cover rounded-lg border border-slate-300 shadow-md">
                        </div>

                        <div id="newImagePreviewContainer" class="hidden">
                             <p class="text-xs text-slate-500 mb-1">New Selection:</p>
                            <img id="newImagePreview" 
                                 class="w-40 h-28 object-cover rounded-lg border border-indigo-400 shadow-md">
                        </div>
                        
                        <div class="flex-1 min-w-[200px]">
                            <label for="imageInput" class="text-sm text-slate-500 block mb-1">Upload New Image (Optional)</label>
                            <input type="file" id="imageInput" name="image"
                                   class="w-full border border-slate-300 rounded-lg px-4 py-2.5 shadow-sm cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="sticky bottom-0 bg-white/90 border-t border-slate-200 px-6 py-4 backdrop-blur-md flex justify-end gap-3">
            
            <button onclick="closeModal()" type="button"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-lg transition active:scale-95 shadow-sm">
                Cancel
            </button>

            <button form="newsForm" type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-2.5 rounded-lg shadow-md shadow-indigo-200 active:scale-95 transition">
                <span id="modalAction">Save</span>
            </button>
        </div>
    </div>
</div>


<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
let quill;

// OPEN MODAL
async function openModal(mode, postId = null) {
    const overlay = document.getElementById("modalOverlay");
    const form = document.getElementById("newsForm");
    
    // Reset form and hide previews
    form.reset();
    document.getElementById("currentImageContainer").classList.add("hidden");
    document.getElementById("newImagePreviewContainer").classList.add("hidden");

    overlay.classList.remove("hidden");
    lucide.createIcons();

    // 1. Initialize WYSIWYG if not loaded
    if (!quill) {
        quill = new Quill('#quillEditor', {
            theme: 'snow',
            placeholder: 'Write the full article content here...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['link'],
                    [{ 'color': [] }, { 'background': [] }],
                    ['clean']
                ]
            }
        });
    }

    // 2. Clear editor content
    quill.setContents([]);
    form.action = "sections/news_save.php";

    if (mode === "add") {
        document.getElementById("modalTitle").innerText = "Add New News Article";
        document.getElementById("modalAction").innerText = "Save";
        return;
    }

    // 3. EDIT MODE logic
    if (mode === "edit") {
        document.getElementById("modalTitle").innerText = "Edit News Article";
        document.getElementById("modalAction").innerText = "Update";

        try {
            const res = await fetch("sections/news_fetch.php?id=" + postId);
            const data = await res.json();

            if (!data.success) {
                alert("Failed to load article data.");
                return;
            }

            const p = data.post;

            // Populate text inputs
            document.getElementById("postId").value = p.id;
            document.getElementById("title").value = p.title;
            document.getElementById("subheading").value = p.subheading;
            document.getElementById("author").value = p.author;
            document.getElementById("category").value = p.category;
            document.getElementById("description").value = p.description;

            // Load Quill content (handles null/empty content gracefully)
            quill.root.innerHTML = p.content || ''; 

            // Image Display Logic (FIXED/IMPROVED)
            const currentImagePreview = document.getElementById("currentImagePreview");
            const currentImageContainer = document.getElementById("currentImageContainer");
            
            // Prefer 'image_url' if provided by the backend, fallback to hardcoded path
            if (p.image_url) {
                // NEW WAY: Absolute URL provided by news_fetch.php
                currentImagePreview.src = p.image_url;
                currentImageContainer.classList.remove("hidden");
            } else if (p.image) {
                // OLD WAY: Fallback using hardcoded relative path
                currentImagePreview.src = "../../uploads/news/" + p.image;
                currentImageContainer.classList.remove("hidden");
            }

        } catch (err) {
            console.error(err);
            alert("Error loading news data.");
        }
    }
}

// -------- IMAGE PREVIEW for NEW UPLOAD --------
document.getElementById("imageInput").addEventListener("change", function () {
    const file = this.files[0];
    const newPreview = document.getElementById("newImagePreview");
    const newContainer = document.getElementById("newImagePreviewContainer");

    if (file) {
        newPreview.src = URL.createObjectURL(file);
        newContainer.classList.remove("hidden");
    } else {
        newContainer.classList.add("hidden");
    }
});

// CLOSE MODAL
function closeModal() {
    document.getElementById("modalOverlay").classList.add("hidden");
    // Stop any animations or processes if necessary
}

// BEFORE SUBMIT — move Quill HTML into hidden textarea
document.getElementById("newsForm").addEventListener("submit", function () {
    // Get the HTML content from Quill editor and put it into the hidden textarea
    document.getElementById("content").value = quill.root.innerHTML;
});
</script>