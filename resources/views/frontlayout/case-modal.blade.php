<div class="adm-modal" id="caseModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="modal-head">
            <h4><i class="fa-solid fa-gavel"></i> New Case</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form class="adm-form" data-static-form>
            <div class="f-grid">
                <div class="f-group">
                    <label>Case Title</label>
                    <input type="text" placeholder="e.g. State vs Rohit S." required>
                </div>
                <div class="f-group">
                    <label>Client Name</label>
                    <input type="text" placeholder="Client full name" required>
                </div>
                <div class="f-group">
                    <label>Practice Area</label>
                    <select required>
                        <option value="">Select</option>
                        @foreach(config('practice', []) as $a)
                            <option>{{ $a['title'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="f-group">
                    <label>Assign Advocate</label>
                    <select>
                        <option>Arjun Mehta</option>
                        <option>Neha Kapoor</option>
                        <option>Priya Sharma</option>
                    </select>
                </div>
                <div class="f-group">
                    <label>Court</label>
                    <input type="text" placeholder="e.g. Saket District Court">
                </div>
                <div class="f-group">
                    <label>Next Hearing</label>
                    <input type="date">
                </div>
                <div class="f-group full">
                    <label>Notes</label>
                    <textarea rows="3" placeholder="Brief about the matter"></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-gold"><i class="fa-solid fa-check"></i> Save Case</button>
            </div>
        </form>
    </div>
</div>