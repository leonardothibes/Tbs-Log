# Makefile - A courtesy of Tbs\Log.
#
# A collection of reusable tasks for automating some process.
#
# Make sure you have GNU Make and GNU curl, and then type "make" in this Makefile folder.
#

# General Configuration
NAME       = "Tbs\\Log"
STANDARD   = "PSR2"
DATE       = `date "+%Y-%m-%d"`
BASEDIR    = `pwd`
BIN        = "${BASEDIR}/bin"
SRC        = "${BASEDIR}/src"
DOCS       = "${BASEDIR}/docs"
BUILD      = "${BASEDIR}/build"
TESTS      = "${BASEDIR}/tests"
VENDOR     = "${BASEDIR}/vendor"
LOGS       = "${BASEDIR}/logs"
LOGFILE    = "${LOGS}/debug_${DATE}.log"
URI        = "leonardothibes/Tbs-Log"
DOCUMENTUP = "http://documentup.com/${URI}"
GITHUB     = "http://github.com/${URI}"

build: .clear .title lint code-sniffer test-analyze phpmd phpdoc documentup
	@echo ""
	@echo " - BUILD SUCCESS!"
	@echo ""

rw:
	@[ -d ${BIN}     ] || mkdir ${BIN}
	@[ -d ${BUILD}   ] || mkdir ${BUILD}
	@[ -d ${LOGS}    ] || mkdir ${LOGS}
	@[ -f ${LOGFILE} ] || > ${LOGFILE}
	@chmod -R 777 ${LOGS}

clean:
	@rm -Rf ${LOGS}/*
	@rm -Rf ${BUILD}/*
	@find ${BASEDIR} | grep .DS_Store | xargs rm -f
	@find ${BASEDIR} | grep Thumbs.db | xargs rm -f

clean-all:
	@rm -Rf ${VENDOR}
	@rm -Rf ${BUILD}
	@rm -Rf ${LOGS}
	@rm -Rf ${BIN}

.composer: rw
	@if [ ! -f ${BIN}/composer.phar ]; then \
		curl -sS https://getcomposer.org/installer | php -- --install-dir=${BIN}; \
	fi; \

install: rw .clear .composer
	@php ${BIN}/composer.phar install --no-dev

install-dev: rw .clear .phpDocumentor .composer
	@php ${BIN}/composer.phar install --dev

classmap:
	@php ${BIN}/composer.phar dump-autoload

lint: .clear
	@for file in `find ./src` ; do \
		results=`php -l $$file`; \
		if [ "$$results" != "No syntax errors detected in $$file" ]; then \
			echo $$results; \
			echo ""; \
			exit 1; \
		fi; \
	done;
	@echo " - No syntax errors detected"
	
test: rw .clear
	@${BIN}/phpunit -c ${TESTS}/phpunit.xml ${TESTS}

testdox: rw .clear
	@${BIN}/phpunit -c ${TESTS}/phpunit.xml --testdox ${TESTS}

test-analyze: rw .clear
	@${BIN}/phpunit -c ${TESTS}/phpunit.xml  \
		--testdox-html=${BUILD}/testdox.html \
		--coverage-html=${BUILD}/coverage    \
		${TESTS} 1> /dev/null 2> /dev/null
	@echo " - Test reports generated!"

code-sniffer: .clear
	@${BIN}/phpcs --standard=${STANDARD} ${SRC}
	@echo " - No code standards violation detected"

pdepend: rw .clear
	@${BIN}/pdepend --jdepend-chart=${BUILD}/pdepend/dependencies.svg --overview-pyramid=${BUILD}/pdepend/overview-pyramid.svg ${SRC}
	@echo " - Software metrics generated"

phpmd: rw .clear
	@trap "${BIN}/phpmd --suffixes php ${SRC} html cleancode,codesize,controversial,design,naming,unusedcode --reportfile ${BUILD}/pmd.html" EXIT
	@echo " - Mess detector report generated"

phpcpd: rw .clear
	@trap "${BIN}/phpcpd --log-pmd=${BUILD}/phpcpd.xml ${SRC}" EXIT

phpdcd:

.phpDocumentor:
	@[ -f ${BIN}/phpDocumentor.phar ] || curl http://phpdoc.org/phpDocumentor.phar -o ${BIN}/phpDocumentor.phar
	@[ -f ${BIN}/phpDocumentor.phar ] && chmod 755 ${BIN}/phpDocumentor.phar

phpdoc: rw .clear .phpDocumentor
	@php ${BIN}/phpDocumentor.phar -d ${SRC} -t ${BUILD}/apidoc 1> /dev/null 2> /dev/null
	@echo " - API documentation generated"

documentup:
	@echo " - Recompiling online documentation on ${DOCUMENTUP}"
	@curl -X GET ${DOCUMENTUP}/recompile 1> /dev/null 2> /dev/null

.clear:
	@clear

.title:
	@echo "Makefile - A courtesy of ${NAME}."
	@echo ""

help: .clear
	@echo "Usage: make [options]"
	@echo ""
	@echo "  main(default)     General project build"
	@echo "  help              Show this HELP message"
	@echo ""