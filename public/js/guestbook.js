function setVisitorType(type) {
    document.getElementById('visitor_type').value = type;
    document.getElementById('card-internal').classList.toggle('active', type === 'internal');
    document.getElementById('card-external').classList.toggle('active', type === 'external');
    document.getElementById('field-department').classList.toggle('hidden', type !== 'internal');
    document.getElementById('field-external').classList.toggle('hidden', type !== 'external');
    document.getElementById('department_id').required = (type === 'internal');
    document.getElementById('external_agency').required = (type === 'external');
}

const oldType = document.getElementById('visitor_type').value;
if (oldType) setVisitorType(oldType);

function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('live-clock').textContent = time;
    document.getElementById('live-date').textContent = date;
}
updateClock();
setInterval(updateClock, 1000);