<div class="modal animated zoomIn" id="create-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Create Event</h6>
                </div>
                <div class="modal-body">
                    <form id="save-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-6 p-1">
                                <label class="form-label">Date *</label>
                                <input type="date" class="form-control" id="eventDate">
                            </div>
                            <div class="col-6 p-1">
                                <label class="form-label">Time *</label>
                                <input type="time" class="form-control" id="eventTime">
                            </div>
                            <div class="col-6 p-1">
                                <label class="form-label">Category *</label>
                                <select name="type" id="eventCategory" class="form-control">
                                    <option value="">--select--</option>
                                    {{-- @forelse ($categories as $categorie)
                                        <option value="{{ $categorie->id }}">{{ $categorie->name }}</option>
                                    @empty
                                        <option value="Income">No Category Found!</option>
                                    @endforelse --}}
                                    
                                </select>
                            </div>
                            <div class="col-6 p-1">
                                <label class="form-label">Type *</label>
                                <select name="type" id="eventType" class="form-control">
                                    <option value="">--select--</option>
                                    <option value="Feature">Feature</option>
                                    <option value="Recent">Recent</option>
                                </select>
                            </div>
                            <div class="col-6 p-1">
                                <label class="form-label">Title *</label>
                                <input type="text" class="form-control" id="eventTitle" placeholder="Title">
                            </div>
                            <div class="col-6 p-1">
                                <label class="form-label">Location *</label>
                                <input type="text" class="form-control" id="eventLocation" placeholder="Location">
                            </div>
                            <div class="col-12 p-1">
                                <label class="form-label">Teaser <small class="text-muted">(10-20 words, 1-2 lines)</small></label>
                                <textarea type="text" class="form-control" id="eventTeaser" placeholder="Enter a short teaser for your event (10-20 words)" rows="2" maxlength="255"></textarea>
                                <small class="text-muted">Word count: <span id="teaserWordCount">0</span></small>
                            </div>
                            <div class="col-12 p-1">
                                <label class="form-label">Description </label>
                                <textarea type="text" class="form-control" id="eventDescription" placeholder="Description"></textarea>
                            </div>
                            
                            
                            <div class="col-12 p-1">
                                <br/>
                                <img class="w-15" id="newImg" src="{{asset('backend/images/default.jpg')}}"/>
                                <br/>

                                <label class="form-label">Thumbnail *</label>
                                <input oninput="newImg.src=window.URL.createObjectURL(this.files[0])" type="file" class="form-control" id="eventImage" onchange="validateFileType()" accept="image/png, image/jpeg, image/jpg">
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal-close" class="btn btn-sm btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                    <button onclick="Save()" id="save-btn" class="btn btn-sm  btn-success" >Save</button>
                </div>
            </div>
    </div>
</div>


<script>
    FillCategoryDropDown();

    // Add word count functionality for teaser
    document.getElementById('eventTeaser').addEventListener('input', function() {
        const text = this.value.trim();
        const wordCount = text === '' ? 0 : text.split(/\s+/).length;
        document.getElementById('teaserWordCount').textContent = wordCount;
        
        // Optional: Add visual feedback for word count
        const countElement = document.getElementById('teaserWordCount');
        if (wordCount >= 10 && wordCount <= 20) {
            countElement.style.color = 'green';
        } else if (wordCount > 20) {
            countElement.style.color = 'red';
        } else {
            countElement.style.color = 'orange';
        }
    });

async function FillCategoryDropDown(){
    let res = await axios.get("/list-category")
    res.data.forEach(function (item,i) {
        let option=`<option value="${item['id']}">${item['name']}</option>`
        $("#eventCategory").append(option);
    })
}
    

    async function Save() {

        let eventDate = document.getElementById('eventDate').value;
        let eventTime = document.getElementById('eventTime').value;
        let eventType = document.getElementById('eventType').value;
        let eventCategory = document.getElementById('eventCategory').value;
        let eventTitle = document.getElementById('eventTitle').value;
        let eventLocation = document.getElementById('eventLocation').value;
        let eventTeaser = document.getElementById('eventTeaser').value;
        let eventDescription = document.getElementById('eventDescription').value;
        let eventImage = document.getElementById('eventImage').files[0];

        //alert(isNaN(parseFloat(eventTitle)))
        if (eventDate.length === 0) {
            errorToast("Date Required !")
        } else if (eventTime.length === 0) {
            errorToast("Time Required !")
        }else if (eventType.length === 0) {
            errorToast("Event Type Required !")
        }else if (eventCategory.length === 0) {
            errorToast("Event Category Required !")
        }else if (eventTitle.length === 0 ) {
            errorToast("Event Title Required !")
        }else if (eventLocation.length === 0 ) {
            errorToast("Event Location Required !")
        }else if (eventDescription.length === 0 ){
            errorToast("Event Description Required !")
        }else if (!eventImage ) {
            errorToast("Event Image Required !")
        }
        else {
        
            document.getElementById('modal-close').click();
            let formData=new FormData();
            formData.append('image',eventImage)
            formData.append('type',eventType)
            formData.append('title',eventTitle)
            formData.append('teaser',eventTeaser)
            formData.append('description',eventDescription)
            formData.append('date',eventDate)
            formData.append('time',eventTime)
            formData.append('location',eventLocation)
            formData.append('categorie_id',eventCategory)

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }
            showLoader();
            let res = await axios.post("/create-event",formData,config)
            hideLoader();
            if(res.status===201){
                successToast('Request completed');
                document.getElementById("save-form").reset();
                document.getElementById('newImg').src="{{asset('backend/images/default.jpg')}}";
                document.getElementById('teaserWordCount').textContent = '0';
                document.getElementById('teaserWordCount').style.color = '';
                await getList();
                }
                else{
                errorToast("Request fail !")
                }
            
        }
    }
    function validateFileType(){
        var fileName = document.getElementById("eventImage").value;
            var idxDot = fileName.lastIndexOf(".") + 1;
            var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
            var allowedExt = ['jpg','jpeg','png'];
            if (allowedExt.indexOf(extFile) != -1 ){
                //successToast('File attachment completed');
            }else{
                errorToast('Only JPG,JPEG,PNG files are allowed');
                document.getElementById("eventImage").value='';
                return;
              }   
        }
</script>
