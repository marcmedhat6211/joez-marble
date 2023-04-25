const faqPage = $("#faqs_page");
const faqCard = faqPage.find(".faq-card");

$(document).ready(function () {
    body.on("click", "[data-go-to]", function (e) {
        e.preventDefault();
        const goTo = $(this).data('go-to');
        const goToElement = $(goTo);
        goToElement.closest(".card").addClass("active");
        $([document.documentElement, document.body]).animate({
            scrollTop: goToElement.offset().top - 10
        }, 1000);
    });

    body.on("click", ".questions-collection-container .card", function (e) {
        $(this).removeClass("active");
    });

    body.on("click", document, function (e) {
        if (
            $(e.target).is("#faqs_search_dropdown")
            ||
            $(e.target).is("#faqs_search_dropdown *")
        ) {
            return;
        }

        $("#faqs_search_dropdown").removeClass("show");
    });

    body.on("input", "#faqs_search_input", function () {
        const inputVal = $(this).val();
        const questionTexts = faqCard.find(".question-text");
        let results = [];
        questionTexts.each(function () {
            const questionText = $(this);
            const questionCard = questionText.closest(".faq-card");
            const questionId = questionCard.attr("id");
            const questionVal = questionText.text();
            if (inputVal.trim().length < 2) {
                drawSingleSearchResult({
                    hasResults: false,
                    empty: true,
                });
            } else if (questionVal.toLowerCase().includes(inputVal.toLowerCase())) {
                results.push({
                    questionVal: questionVal,
                    questionId: questionId
                });
                drawSingleSearchResult({
                    hasResults: true,
                    empty: false,
                    results: results
                });
            }
        });

        if (results.length === 0) {
            drawSingleSearchResult({
                hasResults: false,
                empty: false
            });
        }
    });
});

const drawSingleSearchResult = (resultInfo) => {
    const searchDropdown = $("#faqs_search_dropdown");
    const ul = searchDropdown.find("ul");
    ul.empty();
    searchDropdown.addClass("show");
    if (!resultInfo.hasResults && resultInfo.empty) {
        const enterTwoCharsMessages = $("<p>", {
            class: "text-center py-4 mb-0",
            text: "Please enter at least 2 characters"
        });
        enterTwoCharsMessages.appendTo(ul);
        return;
    } else if (!resultInfo.hasResults) {
        const noResultsMessage = $("<p>", {
            class: "text-center py-4 mb-0",
            text: "No Results Found!"
        });
        noResultsMessage.appendTo(ul);
        return;
    }

    const results = resultInfo.results;
    results.forEach(function (result) {
        const questionTextVal = result.questionVal;
        const questionId = result.questionId;
        const li = $("<li>");
        const searchLink = $("<a>", {
            text: questionTextVal
        }).attr({
            class: "search-link",
            href: "#",
            "data-go-to": `#${questionId}`,
        });

        searchLink.appendTo(li);
        li.appendTo(ul);
    });
};