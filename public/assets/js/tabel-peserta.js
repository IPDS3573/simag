    $('#pendaftar').css('border-radius', '0px')
    $('#pendaftar').css('border-top-left-radius', '10px')
    $('#pendaftar').css('border-top-right-radius', '10px')
    $('#pendaftar').css('background-color', '#508b90')
    $('#aktif').css('border-radius', '0px')
    $('#aktif').css('border-top-left-radius', '10px')
    $('#aktif').css('border-top-right-radius', '10px')
    $('#aktif').css('background-color', '#fff')
    $('#deaktif').css('border-radius', '0px')
    $('#deaktif').css('border-top-left-radius', '10px')
    $('#deaktif').css('border-top-right-radius', '10px')
    $('#deaktif').css('background-color', '#fff')
    $('#pendaftar').click((e) => {
        $('#aktif').removeClass('active')
        $('#deaktif').removeClass('active')
        $(e.target).addClass('active')
        $(e.target).css('background-color', '#508b90')
        $('#aktif').css('background-color', '#fff')
        $('#deaktif').css('background-color', '#fff')
        $('#tabel-pendaftar').css('display', 'block')
        $('#tabel-aktif').css('display', 'none')
        $('#tabel-deaktif').css('display', 'none')

    })
    $('#aktif').click((e) => {
        $('#pendaftar').removeClass('active')
        $('#deaktif').removeClass('active')
        $(e.target).addClass('active')
        $(e.target).css('background-color', '#508b90')
        $('#pendaftar').css('background-color', '#fff')
        $('#deaktif').css('background-color', '#fff')
        $('#tabel-pendaftar').css('display', 'none')
        $('#tabel-aktif').css('display', 'block')
        $('#tabel-deaktif').css('display', 'none')
    })
    $('#deaktif').click((e) => {
        $('#pendaftar').removeClass('active')
        $('#aktif').removeClass('active')
        $(e.target).addClass('active')
        $(e.target).css('background-color', '#508b90')
        $('#pendaftar').css('background-color', '#fff')
        $('#aktif').css('background-color', '#fff')
        $('#tabel-pendaftar').css('display', 'none')
        $('#tabel-aktif').css('display', 'none')
        $('#tabel-deaktif').css('display', 'block')
    })