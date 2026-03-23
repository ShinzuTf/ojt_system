@if(auth()->check() && auth()->user()->must_change_password)
    {{-- Force Password Change Modal with Backdrop --}}
    <div id="forcePasswordChangeBackdrop" style="position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; z-index:1050;">
        <div id="forcePasswordChangeModal" style="background:white; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.3); max-width:420px; width:90%; max-height:90vh; overflow-y:auto;">
            <div style="border-bottom:1px solid var(--gray-100); padding:24px;">
                <h2 style="margin:0 0 8px 0; font-size:1.5rem; font-weight:600; color:var(--gray-900);">Set Your Password</h2>
                <p style="margin:0; font-size:0.85rem; color:var(--gray-500);">You can keep your current password or set a new one</p>
            </div>
            <div style="padding:24px;">
                <form id="forcePasswordChangeForm" method="POST" action="{{ route('password.force-change') }}">
                    @csrf
                    
                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:0.875rem; font-weight:500; color:var(--gray-700); margin-bottom:6px;">
                            New Password (Optional)
                        </label>
                        <input type="password" id="new_password" name="password" class="@error('password') input-error @enderror" style="width:100%; padding:10px 12px; border:1px solid var(--gray-300); border-radius:6px; font-size:0.875rem;" placeholder="Leave blank to keep current password" minlength="8">
                        @error('password') <span style="color:#ef4444; font-size:0.75rem; display:block; margin-top:4px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-size:0.875rem; font-weight:500; color:var(--gray-700); margin-bottom:6px;">
                            Confirm Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" style="width:100%; padding:10px 12px; border:1px solid var(--gray-300); border-radius:6px; font-size:0.875rem;" placeholder="Confirm new password (if changing)" minlength="8">
                    </div>

                    <div style="display:flex; gap:12px; justify-content:flex-end;">
                        <button type="submit" name="action" value="keep" style="background-color:#6b7280; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:500; cursor:pointer; transition:background-color 0.2s;">
                            Keep Password
                        </button>
                        <button type="submit" name="action" value="change" style="background-color:#8b5cf6; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:500; cursor:pointer; display:flex; align-items:center; gap:8px; transition:background-color 0.2s;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
