$(document).ready(function () {
    dashboardDataInfo();
});

function dashboardDataInfo() {
    $.ajax({
        url: dashboardDataInfoUrl,
        type: 'post',
        success: function (response) {
            if (response.status) {
                $.each(response.data.data, function (key, value) {
                    $('#' + key).html(value);
                });

                let size1Data = response.data.ads.size_1;
                if (size1Data) {
                    let card1 = "";
                    $.each(response.data.ads.size_1, function (key, value) {
                        card1 += `
                            <div class="col-md-4">
                                <div class="card text-center mini-stats-wid">
                                    <a href="${value.url}" target="_blank">
                                        <img src="${basepath + value.image}" class="img-fluid" alt="Ad Image">
                                    </a>
                                </div>
                            </div>`;
                    });
                    $('#size-1-banner').append(card1);
                }

                let size2Data = response.data.ads.size_2;
                if (size2Data) {
                    let card2 = `
                        <div class="card text-cente ad-card">
                            <a href="${size2Data.url}" target="_blank">
                                <img src="${basepath + size2Data.image}" class="img-fluid" alt="Ad Image">
                            </a>
                        </div>`;
                    $('#size-2-banner').append(card2);
                }

                let size3Data = response.data.ads.size_3;
                if (size3Data) {
                    let card3 = `
                        <div class="card text-center ad-card">
                            <a href="${size3Data.url}" target="_blank">
                                <img src="${basepath + size3Data.image}" class="img-fluid" alt="Ad Image" >
                            </a>
                        </div>`;
                    $('#size-3-banner').append(card3);
                }

                let size4Data = response.data.ads.size_4;
                if (size4Data) {
                    let card4 = `
                        <div class="card text-center ad-card">
                            <a href="${size4Data.url}" target="_blank">
                                <img src="${basepath + size4Data.image}" class="img-fluid" alt="Ad Image" >
                            </a>
                        </div>`;
                    $('#size-4-banner').append(card4);
                }
            } else {
                showMessage("error", something_went_wrong);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    });
}
